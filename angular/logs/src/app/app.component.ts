import { NgClass, NgFor, NgIf } from '@angular/common';
import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import * as $ from 'jquery';
import { FormsModule } from '@angular/forms';
import { DomSanitizer } from '@angular/platform-browser';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet, NgIf, NgFor, NgClass, FormsModule],
  templateUrl: './app.component.html',
  styleUrl: './app.component.less'
})
export class AppComponent {
  title = 'logs';
  
  
    // Current list of logs in reverse order
	logs: any[] = [];
  
  // Last log of the list (to request the subsequent ones)
  lastKownLogId = 0;
  
  // Currently selected log (details of one log can be shown at a time)
  displayDetails = null;
  
  // Whether the last log has been downloaded
  bottomReached = false;
  
  // Filters
  categories: any[] = [];
  categoryFilter = "";
  users: any[] = [];
  userFilter = "";
  sections: any[] = [];
  sectionFilter = "";
  loading = false;
  actionFilter = "";
  
  constructor(private sanitizer: DomSanitizer) {
    // Initially load logs
    this.loadMoreLogs();
  }
  
  html(value: string) {
    return this.sanitizer.bypassSecurityTrustHtml(value);
  }
  
  /**
   * Fetches the next logs from the server
   */
  loadMoreLogs() {
    // Flag as loading
    this.loading = true;
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
    });
    const url = ((window as any).loadMoreLogsURL as string).replace('LOG_ID', "" + this.lastKownLogId);
    // Mark request
    $.ajax({
      type: 'GET',
      url: url,
      success: (json, xxx, yyy) => {
        var data = JSON.parse(json);
        var atLeastOneVisible = false;
        // Add all logs to the list
        data.forEach((newLog: any) => {
          // Add log
          this.logs.push(newLog);
          this.lastKownLogId = newLog.id;
          // Update category filter list
          if (this.categories.indexOf(newLog.category) === -1 && newLog.category) {
            this.categories.push(newLog.category);
            this.categories.sort();
          }
          // Update user filter list
          if (this.users.indexOf(newLog.user) === -1 && newLog.user) {
            this.users.push(newLog.user);
            this.users.sort();
          }
          // Update section filter list
          if (this.sections.indexOf(newLog.section) === -1 && newLog.section) {
            this.sections.push(newLog.section);
            this.sections.sort();
          }
          // Check if this log is visible under the current filters
          if (!atLeastOneVisible && this.showLog(newLog)) {
            atLeastOneVisible = true;
          }
        });
        // Check if the last log has been downloaded
        if (data.length < (window as any).logsPerRequest) this.bottomReached = true;
        if (atLeastOneVisible || this.bottomReached) {
          // Done
          this.loading = false;
        } else {
          // No new visible log, try downloading more
          this.loadMoreLogs();
        }
      },
      error: () => {
        alert("Une erreur est survenue. Recharge la page.");
        this.loading = false;
      }
    });
  };
  
  /**
   * [Called from page] Shows/hides the details of a log
   */
  toggleDetails(logId: any) {
    this.displayDetails = this.displayDetails == logId ? null : logId;
  };
  
  /**
   * Refresh the page with the new filter values
   */
  updateFilter() {
    // No need anymore in Angular 18
  };
  
  /**
   * Returns whether a given log is visible with the current filters
   */
  showLog(log: any) {
    return (!this.categoryFilter || log.category === this.categoryFilter) &&
            (!this.userFilter || log.user === this.userFilter) &&
            (!this.sectionFilter || log.section === this.sectionFilter) &&
            (!this.actionFilter || (this.actionFilter === "errors" && log.isError) || (this.actionFilter === "non-errors" && !log.isError));
  };
  
}