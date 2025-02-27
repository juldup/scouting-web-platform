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
  title = 'attendance';
  
  
  
  /* DATA */
  
  // Whether the user can edit the values (must be set in the page)
  canEdit = (window as any).canEdit;
  
  // List of events that are being monitored
  monitoredEvents = (window as any).monitoredEvents;
  
  // List of unmonitored events that can be added
  unmonitoredEvents = (window as any).unmonitoredEvents;
  
  // List of members
  members = (window as any).members;
  
  // Date of the first and last displayed events
  minDate = "0000-00-00";
  maxDate = "9999-99-99";
  
  // Flag for when the page is ready
  pageLoaded = false;
  
  // Formats a date to display it as "DD/MM"
  formatDate = function(date: string) {
    return date.substring(8, 10) + "/" + date.substring(5, 7);
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
    
    // Show page
    this.pageLoaded = true;
  }
  
  /* EDITION */
  
  /**
   * Recomputes the min and max date so that max 10 events are being displayed
   */
  updateTimeframe() {
    if (this.monitoredEvents.length <= 10) {
      this.minDate = "0000-00-00";
      this.maxDate = "9999-99-99";
      return;
    }
    this.minDate = this.monitoredEvents[this.monitoredEvents.length - 10].date;
    this.maxDate = "9999-99-99";
  };
  
  
  
  /**
   * Shifts the displayed events so that the one on the left is now visible
   */
  shiftLeft() {
    if (this.minDate <= this.monitoredEvents[0].date) return;
    var i = this.monitoredEvents.length - 1;
    while (i > 0 && this.monitoredEvents[i].date >= this.minDate) i--;
    this.minDate = this.monitoredEvents[i].date;
    this.maxDate = this.monitoredEvents[Math.min(i + 9, this.monitoredEvents.length - 1)].date;
  };
  
  /**
   * Shifts the displayed events so that the one on the right is now visible
   */
  shiftRight() {
    if (this.maxDate >= this.monitoredEvents[this.monitoredEvents.length - 1].date) return;
    var i = 0;
    while (i < this.monitoredEvents.length - 1 && this.monitoredEvents[i].date <= this.maxDate) i++;
    this.maxDate = this.monitoredEvents[i].date;
    this.minDate = this.monitoredEvents[Math.max(i - 9, 0)].date;
  };
  
  /**
   * Changes the attendance status to an event for a member
   */
  toggle(member : any, event : any) {
    if (!(window as any).canEdit) return;
    member.status["event_" + event.id] = (member.status["event_" + event.id] + 1) % 3;
    this.uploadChanges();
  };
  
  /**
   * Set the attendance status of all members to the given value
   */
  setAll(event : any, status : any) {
    if (!(window as any).canEdit) return;
    // Check if there are members with different statuses
    var noneAttended = true;
    var allAttended = true;
    this.members.forEach((member: any) => {
      if (member.status["event_" + event.id] != 0) {
        noneAttended = false;
      }
      if (member.status["event_" + event.id] != 1) {
        allAttended = false;
      }
    });
    if (!noneAttended && !allAttended) {
      if (!confirm("Il y a déjà des présences/absences encodées pour cet événement. Les effacer ?")) {
        return;
      }
    }
    // Apply change
    this.members.forEach((member : any) => {
      member.status["event_" + event.id] = status;
    });
    this.uploadChanges();
  };
  
  /**
   * Returns the number of members that have the given status for an event
   */
  countWithStatus(event : any, status : any) {
    var total = 0;
    this.members.forEach((member : any) => {
      if (member.status["event_" + event.id] == status) total++;
    });
    return total;
  };
  
  /**
   * Returns the number of events with the given status for a member
   */
  countMemberStatus(member : any, status : any) {
    var total = 0;
    this.monitoredEvents.forEach((event : any) => {
      if (member.status["event_" + event.id] == status) total++;
    });
    return total;
  };
  
  /**
   * Sorts a list of events by date
   */
  sortEvents(events : any[]) {
    events.sort((a, b) => {
      if (a.date < b.date) return -1;
      if (a.date > b.date) return 1;
      // Same date, sort by id
      return a.id - b.id;
    });
  };
  
  /**
   * Removes an event from the monitored list
   */
  remove(event : any) {
    if (!(window as any).canEdit) return;
    if (confirm("Supprimer l'activité \"" + event.title + "\" du " + this.formatDate(event.date) + " de la liste des présences ?")) {
      this.monitoredEvents.splice(this.monitoredEvents.indexOf(event), 1);
      this.unmonitoredEvents.push(event);
      this.sortEvents(this.unmonitoredEvents);
      this.updateTimeframe();
      //this.$$phase || this.$apply();
      this.uploadChanges();
    }
    return false;
  };
  
  /**
   * Adds an event to the monitored list
   */
  selectedUnmonitoredEvent = null;
  addUnmonitoredEvent() {
    if (!(window as any).canEdit) return;
    if (this.selectedUnmonitoredEvent == null) return;
    for (var i = 0; i < this.unmonitoredEvents.length; i++) {
      var event = this.unmonitoredEvents[i];
      if (event.id == this.selectedUnmonitoredEvent) {
        this.unmonitoredEvents.splice(i, 1);
        this.monitoredEvents.push(event);
        this.sortEvents(this.monitoredEvents);
        this.updateTimeframe();
        this.members.forEach((member : any) => {
          // Make sur member.status is a non-array object
          if (!member.status || Array.isArray(member.status)) member.status = {};
          member.status["event_" + this.selectedUnmonitoredEvent] = 0;
        });
        //this.$$phase || this.$apply();
        this.uploadChanges();
        // Reset select
        setTimeout(() => {
          this.unmonitoredEvents = this.unmonitoredEvents.filter((event: any) => event.id != this.selectedUnmonitoredEvent);
          this.selectedUnmonitoredEvent = null;
        }, 0);
        return;
      }
    }
  };
  
  /* SYNCHRONIZATION */
  
  // Uploading status
  uploading = false;
  
  // True if some changes are yet unsynchronized
  unsynchronized = false;
  
  // Change counter (to avoid uploading when more recent changes have been made)
  uploadId = 0;
  
  updateModel = function() {};
  
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
    // Gather events
    var events: any[] = [];
    this.monitoredEvents.forEach((event: any) => { events.push({id: event.id, monitored: true}); });
    this.unmonitoredEvents.forEach((event: any) => { events.push({id: event.id, monitored: false}); });
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
        // Upload now
        this.uploading = true;
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': token,
          }
        })
        $.ajax({
          type: "POST",
          url: (window as any).commitAttendanceChangesURL,
          data: {
            'data': JSON.stringify(this.members),
            'events': JSON.stringify(events)
          }
        }).done((json) => {
          try {
            var data = JSON.parse(json);
            var errorMessage = null;
            if (data.result === "Success") {
              // Upload was successful
              // Add new excused
              if (data.newExcused) {
                data.newExcused.forEach((item: any) => {
                  var newExcusedData = item.split(":");
                  this.members.forEach((member: any) => {
                    if (member.id == newExcusedData[1]) {
                      member.status["event_" + newExcusedData[0]] = 2;
                    }
                  });
                });
                //this.$$phase || this.$apply();
              }
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
            console.error("Error while saving attendance.", err);
            // On error, reload the page so the user can see what has actually been saved
            alert(errorMessage ? errorMessage : "Une erreur est survenue lors de l'enregistrement des présences.");
            // Reload page
            (window as any).location = (window as any).location;
          }
        });
      }
    }, 1000); // Upload in 1 second
  };
  
}