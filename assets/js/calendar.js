const date = new Date();
const prevDay = new Date().getDate() - 1;
const nextDay = new Date(new Date().getTime() + 24 * 60 * 60 * 1000);

// prettier-ignore
const nextMonth = date.getMonth() === 11 ? new Date(date.getFullYear() + 1, 0, 1) : new Date(date.getFullYear(), date.getMonth() + 1, 1)
// prettier-ignore
const prevMonth = date.getMonth() === 11 ? new Date(date.getFullYear() - 1, 0, 1) : new Date(date.getFullYear(), date.getMonth() - 1, 1)
let calendarCategories = [
  {
    label: 'Business',
    value: 'business',
    activeClass: 'ring-primary-500 bg-primary-500',
    className: 'group-hover:border-blue-500',
  },
  {
    label: 'Personal',
    value: 'personal',
    activeClass: 'ring-success-500 bg-success-500',
    className: ' group-hover:border-green-500',
  },
  {
    label: 'Holiday',
    value: 'holiday',
    activeClass: 'ring-danger-500 bg-danger-500',
    className: ' group-hover:border-red-500',
  },
  {
    label: 'Family',
    value: 'family',
    activeClass: 'ring-info-500 bg-info-500',
    className: ' group-hover:border-cyan-500',
  },
  {
    label: 'Meeting',
    value: 'meeting',
    activeClass: 'ring-warning-500 bg-warning-500',
    className: ' group-hover:border-yellow-500',
  },
  {
    label: 'Etc',
    value: 'etc',
    activeClass: 'ring-info-500 bg-info-500',
    className: ' group-hover:border-cyan-500',
  },
];

function calendar() {
  return {
    categories: [
      {
        label: 'Business',
        value: 'business',
        className: 'data-[state=checked]:bg-primary data-[state=checked]:ring-primary',
      },
      {
        label: 'Personal',
        value: 'personal',
        className: 'data-[state=checked]:bg-success data-[state=checked]:ring-success',
      },
      {
        label: 'Holiday',
        value: 'holiday',
        className: 'data-[state=checked]:bg-destructive data-[state=checked]:ring-destructive',
      },
      {
        label: 'Family',
        value: 'family',
        className: 'data-[state=checked]:bg-info data-[state=checked]:ring-info',
      },
      {
        label: 'Meeting',
        value: 'meeting',
        className: 'data-[state=checked]:bg-warning data-[state=checked]:ring-warning',
      },
      {
        label: 'Etc',
        value: 'etc',
        className: 'data-[state=checked]:bg-info data-[state=checked]:ring-info',
      },
    ],
    selectedCategories: ['business', 'personal', 'holiday', 'family', 'meeting', 'etc'], // Start with all selected
    selectAll: true,
    calendarInstance: null,
    openModal: false,
    openedWithKeyboard : false,

    // Method to initialize the calendar
    initCalendar() {
      var containerEl = document.getElementById('external-events');
      var calendarEl = document.getElementById('calendar');
      var Draggable = FullCalendar.Draggable;

      new Draggable(containerEl, {
        itemSelector: '.fc-event',
        eventData: function (eventEl) {
          return {
            title: eventEl.innerText,
          };
        },
      });
      this.calendarInstance = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay',
        },
        events: this.getFilteredEvents(), // Load events based on selected categories
        editable: true,
        selectable: true,
        selectMirror: true,
        dayMaxEvents: 2,
        weekends: true,
        eventClassNames: handleClassName,
        droppable: true,
        contentHeight: 'auto',
      });
      this.calendarInstance.render();
    },
    renderCalendar() {
      if (this.isVisible && this.calendarInstance) {
        this.calendarInstance.render();
        this.calendarInstance.updateSize();
      }
    },

    setupVisibilityObserver() {
      const observer = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              this.isVisible = true;
              this.renderCalendar();
              observer.disconnect(); // Stop observing once visible
            }
          });
        },
        { threshold: 0.1 }
      ); // Trigger when 10% of the element is visible

      observer.observe(this.$el);
    },
    // Method to get events filtered by selected categories
    getFilteredEvents() {
      const allEvents = [
        {
          id: 'calendar-event-1',
          title: 'All Day Event',
          start: new Date(),
          end: new Date(new Date().getTime() + 24 * 60 * 60 * 1000),
          allDay: false,
          extendedProps: {
            calendar: 'business',
          },
        },
        {
          id: 'calendar-event-2',
          title: 'Meeting With Client',
          start: new Date(new Date().getFullYear(), new Date().getMonth() + 1, -11),
          end: new Date(new Date().getFullYear(), new Date().getMonth() + 1, -10),
          allDay: true,
          extendedProps: {
            calendar: 'personal',
          },
        },
        {
          id: 'calendar-event-3',
          title: 'Lunch',
          allDay: true,
          start: new Date(new Date().getFullYear(), new Date().getMonth() + 1, -9),
          end: new Date(new Date().getFullYear(), new Date().getMonth() + 1, -7),
          extendedProps: {
            calendar: 'family',
          },
        },
        {
          id: 'calendar-event-4',
          title: 'Birthday Party',
          start: new Date(new Date().getFullYear(), new Date().getMonth() + 1, -11),
          end: new Date(new Date().getFullYear(), new Date().getMonth() + 1, -10),
          allDay: true,
          extendedProps: {
            calendar: 'meeting',
          },
        },
        {
          id: 'calendar-event-5',
          title: 'Holiday',
          start: new Date(new Date().getFullYear(), new Date().getMonth() + 1, -13),
          end: new Date(new Date().getFullYear(), new Date().getMonth() + 1, -12),
          allDay: true,
          extendedProps: {
            calendar: 'holiday',
          },
        },
        {
          id: 'calendar-event-6',
          title: 'Monthly Meeting',
          start: new Date(new Date().getFullYear(), new Date().getMonth() + 1, 1),
          end: new Date(new Date().getFullYear(), new Date().getMonth() + 1, 1),
          allDay: true,
          extendedProps: {
            calendar: 'business',
          },
        },
      ];

      // Filter events based on selected categories
      return allEvents.filter((event) =>
        this.selectedCategories.includes(event.extendedProps.calendar)
      );
    },

    // Toggle the selection of individual categories
    toggleCategory(event) {
      const category = event.target.value;
      if (event.target.checked) {
        this.selectedCategories.push(category);
      } else {
        this.selectedCategories = this.selectedCategories.filter((c) => c !== category);
      }

      // Update selectAll based on the current selection
      this.selectAll = this.selectedCategories.length === this.categories.length;

      // Update the calendar events
      this.updateCalendarEvents();
    },

    // Toggle all categories
    toggleAll(event) {
      if (event.target.checked) {
        this.selectedCategories = this.categories.map((c) => c.value);
      } else {
        this.selectedCategories = [];
      }
      this.selectAll = event.target.checked;

      // Update the calendar events
      this.updateCalendarEvents();
    },

    // Update calendar events based on the selected categories
    updateCalendarEvents() {
      this.calendarInstance.removeAllEvents();
      this.calendarInstance.addEventSource(this.getFilteredEvents());
    },
  };
}

// Function to handle event classes based on category
const handleClassName = (arg) => {
  if (arg.event.extendedProps.calendar === 'holiday') {
    return 'danger';
  } else if (arg.event.extendedProps.calendar === 'business') {
    return 'primary';
  } else if (arg.event.extendedProps.calendar === 'personal') {
    return 'success';
  } else if (arg.event.extendedProps.calendar === 'family') {
    return 'info';
  } else if (arg.event.extendedProps.calendar === 'etc') {
    return 'info';
  } else if (arg.event.extendedProps.calendar === 'meeting') {
    return 'warning';
  }
};
