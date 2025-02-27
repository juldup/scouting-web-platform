import { NgClass, NgFor, NgIf } from '@angular/common';
import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import * as $ from 'jquery';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet, NgIf, NgFor, NgClass, FormsModule],
  templateUrl: './app.component.html',
  styleUrl: './app.component.less'
})
export class AppComponent {
  title = 'paiement';
  
  /* DATA */
  
  // Whether the user can edit the values (must be set in the page)
  canEdit = (window as any).canEdit;
  
  // List of events
  events = (window as any).events;
  
  // List of deleted events
  deletedEvents = [];
  
  // List of members
  members = (window as any).members;
  
  // Id of the first and last displayed events
  minId = 0;
  maxId = 999999999;
  
  // Max number of events displayed
  frameSize = 6;
  
  // True once the page is ready
  pageLoaded = true;
  
  // Ajax urls
  deleteEventURL = (window as any).deleteEventURL;
  postNewEventURL = (window as any).postNewEventURL;
  commitPaymentChangesURL = (window as any).commitPaymentChangesURL;
  
  // Input variable bound to html input
  newEventInput = "";
  newEventInputDisabled = false;
  
  /* EDITION */
  
  /**
   * Recomputes the min and max id so that max *frameSize* events are being displayed
   */
  updateTimeframe() {
    if (this.events.length <= this.frameSize) {
      this.minId = 0;
      this.maxId = 999999999;
      return;
    }
    this.minId = this.events[this.events.length - this.frameSize].id;
    this.maxId = 999999999;
  };
  
  constructor() {
    // Initially set the time frame
    this.updateTimeframe();
    // Prevent leaving page before everything is synchronized
    (window as any).onbeforeunload = () => {
      if (this.unsynchronized) {
        return 'Les changements ne sont pas encore tous sauvés. Quitter quand même ?';
      }
      return true;
    };
    jQuery('a').filter('[href!="#"]').on('click', () => {
      if (this.unsynchronized) {
        return confirm('Les changements ne sont pas encore tous sauvés. Quitter quand même ?');
      }
      return true;
    });
    // Page is now ready
    this.pageLoaded = true;
  }
  
  
  /**
   * Shifts the displayed events so that the one on the left is now visible
   */
  shiftLeft() {
    if (this.minId <= this.events[0].id) return;
    var i = this.events.length - 1;
    while (i > 0 && this.events[i].id >= this.minId) i--;
    this.minId = this.events[i].id;
    this.maxId = this.events[Math.min(i + this.frameSize - 1, this.events.length - 1)].id;
  };
  
  /**
   * Shifts the displayed events so that the one on the right is now visible
   */
  shiftRight() {
    if (this.maxId >= this.events[this.events.length - 1].id) return;
    var i = 0;
    while (i < this.events.length - 1 && this.events[i].id <= this.maxId) i++;
    this.maxId = this.events[i].id;
    this.minId = this.events[Math.max(i - (this.frameSize-1), 0)].id;
  };
  
  /**
   * Changes the payment status to an event for a member
   */
  toggle(member: any, event: any) {
    if (!this.canEdit) return;
    member.status["event_" + event.id] = !member.status["event_" + event.id];
    this.uploadChanges();
  };
  
  /**
   * Set the payment status of all members to the given value
   */
  setAll(event: any, status: any) {
    if (!this.canEdit) return;
    // Check if there are members with different statuses
    var nonePaid = true;
    var allPaid = true;
    this.members.forEach((member: any) => {
      if (member.status["event_" + event.id])
        nonePaid = false;
      else
        allPaid = false;
    });
    if (!nonePaid && !allPaid) {
      if (!confirm("Il y a déjà des paiements encodés pour cette activité. Les effacer ?")) {
        return;
      }
    }
    // Apply change
    this.members.forEach((member: any) => {
      member.status["event_" + event.id] = status;
    });
    this.uploadChanges();
  };
  
  /**
   * Returns the number of members that have the given status for an event
   */
  countWithStatus(event: any, status: any) {
    var total = 0;
    this.members.forEach((member: any) => {
      if ((member.status["event_" + event.id] && status) || (!member.status["event_" + event.id] && !status))
        total++;
    });
    return total;
  };
  
  /**
   * Returns the number of events with the given status for a member
   */
  countMemberStatus(member: any, status: any) {
    var total = 0;
    this.events.forEach((event: any) => {
      if ((member.status["event_" + event.id] && status) || (!member.status["event_" + event.id] && !status))
        total++;
    });
    return total;
  };
  
  /**
   * Sorts a list of events by id
   */
  sortEvents(events: any) {
    events.sort((a: any, b: any) => {
      if (a.id < b.id) return -1;
      if (a.id > b.id) return 1;
      return 0;
    });
  };
  
  /**
   * Removes an event from the monitored list
   */
  remove(event: any) {
    // Check if there are members with different statuses
    var somePaid = false;
    this.members.forEach((member: any) => {
      if (member.status["event_" + event.id]) {
        somePaid = true;
      }
    });
    if (somePaid) {
      if (!confirm("Il y a déjà des paiements encodés pour cette activité. La supprimer quand même ?")) {
        return false;
      }
    }
    // Get csrf token
    let token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!token) {
      alert("Une erreur est survenue. La page va être rechargée.");
      window.location = window.location;
    }
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': token,
      }
    })
    // Request delete
    $.ajax({
      url: this.deleteEventURL,
      type: "POST",
      data: {
        eventId: event.id
      },
      success: (data) => {
        this.events = this.events.filter((e: any) => e != event);
        this.updateTimeframe();
      },
      error: (data) => {
        var errorMessage = "Une erreur est survenue lors de la suppression d'une activité.";
        if (data && data.responseJSON && data.responseJSON.errorMessage) errorMessage = data.responseJSON.errorMessage;
        alert(errorMessage);
      }
    });
    return false;
  };
  
  /**
   * Adds an event to the list
   */
  addEvent() {
    // Check new name validity
    var newEventName = this.newEventInput.trim();
    if (!newEventName) {
      alert("Tu dois entrer un nom pour cette activité.");
      return;
    }
    for (var i = 0; i < this.events.length; i++) {
      if (this.events[i].name == newEventName) {
        alert("Cette activité existe déjà. Choisis un autre nom.");
        return;
      }
    }
    // Get csrf token
    let token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!token) {
      alert("Une erreur est survenue. La page va être rechargée.");
      window.location = window.location;
    }
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': token,
      }
    })
    // Submit
    this.newEventInputDisabled = true;
    $.ajax({
      url: this.postNewEventURL,
      type: "POST",
      data: {
        name: newEventName
      },
      success: (data) => {
        this.newEventInputDisabled = false;
        this.newEventInput = "";
        this.events.push({id: data.id, name: newEventName});
        this.members.forEach((member: any) => {
          // Make sur member.status is a non-array object
          if (!member.status || Array.isArray(member.status)) member.status = {};
          member.status["event_" + data.id] = false;
        });
        this.updateTimeframe();
      },
      error: (data) => {
        this.newEventInputDisabled = false;
        var errorMessage = "Une erreur est survenue lors de l'ajout d'une activité.";
        if (data && data.responseJSON && data.responseJSON.errorMessage) errorMessage = data.responseJSON.errorMessage;
        alert(errorMessage);
      }
    });
  };
  
  /* SYNCHRONIZATION */
  
  // Uploading status
  uploading = false;
  
  // True if some changes are yet unsynchronized
  unsynchronized = false;
  
  // Change counter (to avoid uploading when more recent changes have been made)
  uploadId = 0;
  
  /**
   * Uploads the current state to the server
   */
  uploadChanges() {
    // If editing is not allowed, don't upload
    if (!this.canEdit) return;
    // Increment upload counter
    this.uploadId++;
    // Show synchronization icon
    this.unsynchronized = true;
    // Don't upload now if an upload is already running
    if (this.uploading) {
      return;
    }
    // Get current upload
    var uploadId = this.uploadId;
    // Set timeout in a short time, to avoid sending data all the time if
    // others changes are made subsequently
    setTimeout(() => {
      if (uploadId !== this.uploadId) {
        // There are more recent changes, don't upload now
      } else {
        // Get csrf token
        let token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!token) {
          alert("Une erreur est survenue. La page va être rechargée.");
          window.location = window.location;
        }
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': token,
          }
        })
        // Upload now
        this.uploading = true;
        $.ajax({
          type: "POST",
          url: this.commitPaymentChangesURL,
          data: {
            'data': JSON.stringify(this.members)
          }
        }).done((json) => {
          try {
            var data = JSON.parse(json);
            var errorMessage = null;
            if (data.result === "Success") {
              // Upload was successful
              // Stop uploading
              this.uploading = false;
              if (uploadId !== this.uploadId) {
                // Other changes are waiting for upload, upload them
                this.uploadChanges();
              } else {
                // No more pending upload, hide the synchronization icon
                this.unsynchronized = false;
              }
            } else {
              // An error has occured
              console.error(data.message);
              errorMessage = data.message;
              throw "error";
            }
          } catch (err) {
            console.log(err);
            // On error, reload the page so the user can see what has actually been saved
            alert(errorMessage ? errorMessage : "Une erreur est survenue lors de l'enregistrement des paiements.");
            // Reload page
            window.location = window.location;
          }
        });
      }
    }, 1000); // Upload in 1 second
  };
  
}