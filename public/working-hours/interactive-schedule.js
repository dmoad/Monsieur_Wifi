class InteractiveScheduler {
    constructor(options = {}) {
        this.options = {
            container: "#schedule-container",
            onSave: null,
            onSlotCreate: null,
            onSlotUpdate: null,
            onSlotDelete: null,
            initialData: [],
            businessHours: { start: 9, end: 17 },
            businessDays: [
                "monday",
                "tuesday",
                "wednesday",
                "thursday",
                "friday",
            ],
            labels: {},
            ...options,
        };

        // Merge provided labels over defaults
        this.labels = Object.assign({
            title:          'Working Hours',
            subtitle:       'Captive Portal Access Schedule',
            quickSet:       'Quick Set:',
            businessHours:  'Business Hours',
            clearAll:       'Clear All',
            saveSchedule:   'Save Schedule',
            hint:           'Click empty cells to create slots. Drag to move, resize with handles, hover for delete.',
            days: {
                monday: 'Monday', tuesday: 'Tuesday', wednesday: 'Wednesday',
                thursday: 'Thursday', friday: 'Friday', saturday: 'Saturday', sunday: 'Sunday',
            },
            msgOverlap:        'Cannot create slot: overlaps with existing slot',
            msgInvalidMove:    'Invalid position: slot would overlap or exceed bounds',
            msgInvalidResize:  'Invalid resize: would overlap or exceed bounds',
            msgBusinessApplied:'Business hours applied',
            msgCleared:        'All slots cleared',
            msgSaved:          'Schedule saved!',
        }, this.options.labels);

        this.slots = [];
        this.slotIdCounter = 0;
        this.cellWidth = 0;
        this.container = null;

        this.init();
    }

    init() {
        this.container = document.querySelector(this.options.container);
        if (!this.container) {
            throw new Error(`Container ${this.options.container} not found`);
        }

        this.render();
        this.calculateCellWidth();
        this.setupEventListeners();
        this.setupInteractions();

        if (this.options.initialData.length > 0) {
            this.loadData(this.options.initialData);
        }

        window.addEventListener("resize", () => this.calculateCellWidth());
    }

    render() {
        const days = [
            "monday",
            "tuesday",
            "wednesday",
            "thursday",
            "friday",
            "saturday",
            "sunday",
        ];
        const hours = Array.from({ length: 24 }, (_, i) => i);

        const L = this.labels;
        this.container.innerHTML = `
            <div class="schedule-container">
                <div class="schedule-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">${L.title}</h5>
                            <p class="mb-0 text-muted">${L.subtitle}</p>
                        </div>
                        <div class="quick-actions">
                            <small class="text-muted me-2">${L.quickSet}</small>
                            <button class="btn btn-outline-primary btn-sm" data-action="business-hours">${L.businessHours}</button>
                            <button class="btn btn-outline-secondary btn-sm" data-action="clear-all">${L.clearAll}</button>
                        </div>
                    </div>
                </div>
                
                <div class="position-relative d-flex flex-column">
                    <div class="schedule-wrapper d-flex">
                        <div class="schedule-grid flex-1" id="schedule-grid">
                        <!-- Time headers -->
                        <div class="time-header">
                            <div class="time-label"></div>
                            ${hours
                                .map(
                                    (hour) =>
                                        `<div class="time-label">${hour}</div>`
                                )
                                .join("")}
                        </div>
                        
                        <!-- Days -->
                        ${days
                            .map(
                                (day) => `
                            <div class="day-row" data-day="${day}">
                                <div class="day-label">${L.days[day] || this.capitalize(day)}</div>
                                ${hours
                                    .map(
                                        (hour) =>
                                            `<div class="time-cell" data-hour="${hour}"></div>`
                                    )
                                    .join("")}
                            </div>
                        `
                            )
                            .join("")}
                        </div>
                    </div>
                </div>
                
                <div class="p-3 border-top bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            ${L.hint}
                        </small>
                        <button class="btn btn-success btn-sm" data-action="save">
                            <i class="bi bi-check-lg me-1"></i> ${L.saveSchedule}
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    calculateCellWidth() {
        // Use dynamic width based on screen size
        if (window.innerWidth > 1600) {
            // On large screens, calculate actual cell width
            const firstCell = this.container.querySelector(".time-cell");
            if (firstCell) {
                this.cellWidth = firstCell.offsetWidth;
            }
        } else {
            // On smaller screens, use fixed width for horizontal scrolling
            this.cellWidth = 60;
        }
    }

    setupEventListeners() {
        this.container.addEventListener("click", (e) => {
            const action = e.target.dataset.action;

            if (action === "business-hours") {
                this.setBusinessHours();
            } else if (action === "clear-all") {
                this.clearAll();
            } else if (action === "save") {
                this.saveSchedule();
            } else if (
                e.target.classList.contains("time-cell") &&
                !e.target.querySelector(".time-slot")
            ) {
                this.createSlot(e.target);
            } else if (e.target.classList.contains("delete-btn")) {
                const slotId = e.target.closest(".time-slot").dataset.slotId;
                this.deleteSlot(slotId);
            }
        });
    }

    createSlot(cell) {
        const dayRow = cell.closest(".day-row");
        const day = dayRow.dataset.day;
        const hour = parseInt(cell.dataset.hour);

        if (this.hasOverlap(day, hour, hour + 1)) {
            this.showMessage(this.labels.msgOverlap, "error");
            return;
        }

        const slotId = `slot-${this.slotIdCounter++}`;
        const slot = {
            id: slotId,
            day: day,
            startHour: hour,
            endHour: hour + 1,
        };

        this.slots.push(slot);
        this.renderSlot(slot);

        if (this.options.onSlotCreate) {
            this.options.onSlotCreate(slot);
        }
    }

    renderSlot(slot) {
        const dayRow = this.container.querySelector(`[data-day="${slot.day}"]`);
        const startCell = dayRow.querySelector(
            `[data-hour="${slot.startHour}"]`
        );

        const slotElement = document.createElement("div");
        slotElement.className = "time-slot";
        slotElement.dataset.slotId = slot.id;
        slotElement.innerHTML = `
            <div class="resize-handle left"></div>
            <span>${slot.startHour}h - ${slot.endHour}h</span>
            <div class="resize-handle right"></div>
            <div class="delete-btn">&times;</div>
        `;

        const width = (slot.endHour - slot.startHour) * this.cellWidth - 4; // -4px for padding
        slotElement.style.width = `${width}px`;
        slotElement.style.transform = "translate(0px, 0px)"; // Reset transform
        slotElement.setAttribute("data-x", 0);
        slotElement.setAttribute("data-y", 0);

        this.adjustSlotFontSize(slotElement, width);

        startCell.appendChild(slotElement);
        this.setupSlotInteractions(slotElement);
    }

    setupSlotInteractions(slotElement) {
        interact(slotElement)
            .draggable({
                listeners: {
                    start: (event) => {
                        event.target.classList.add("dragging");
                    },
                    move: (event) => {
                        this.handleSlotMove(event);
                    },
                    end: (event) => {
                        event.target.classList.remove("dragging");
                        this.handleSlotMoveEnd(event);
                    },
                },
            })
            .resizable({
                edges: {
                    left: ".resize-handle.left",
                    right: ".resize-handle.right",
                },
                listeners: {
                    move: (event) => {
                        this.handleSlotResize(event);
                    },
                    end: (event) => {
                        this.handleSlotResizeEnd(event);
                    },
                },
            });
    }

    handleSlotMove(event) {
        const target = event.target;
        const x = (parseFloat(target.getAttribute("data-x")) || 0) + event.dx;
        const y = (parseFloat(target.getAttribute("data-y")) || 0) + event.dy;

        target.style.transform = `translate(${x}px, ${y}px)`;
        target.setAttribute("data-x", x);
        target.setAttribute("data-y", y);

        this.updateDropZones(event.clientX, event.clientY);
    }

    handleSlotMoveEnd(event) {
        const target = event.target;
        const slotId = target.dataset.slotId;
        const slot = this.slots.find((s) => s.id === slotId);

        if (!slot) return;

        const rect = target.getBoundingClientRect();
        const gridRect = this.container
            .querySelector("#schedule-grid")
            .getBoundingClientRect();

        const newDay = this.getDayFromY(
            rect.top + rect.height / 2 - gridRect.top
        );
        const newHour = this.getHourFromX(rect.left - gridRect.left - 120); // 120px for day labels, use left edge not center

        if (newDay && newHour !== null) {
            const duration = slot.endHour - slot.startHour;
            const newEndHour = newHour + duration;

            if (
                newEndHour <= 24 &&
                !this.hasOverlap(newDay, newHour, newEndHour, slotId)
            ) {
                const oldSlot = { ...slot };
                slot.day = newDay;
                slot.startHour = newHour;
                slot.endHour = newEndHour;

                target.remove();
                this.renderSlot(slot);

                if (this.options.onSlotUpdate) {
                    this.options.onSlotUpdate(slot, oldSlot);
                }
            } else {
                this.resetSlotPosition(target);
                this.showMessage(this.labels.msgInvalidMove, "error");
            }
        } else {
            this.resetSlotPosition(target);
        }

        this.clearDropZones();
    }

    handleSlotResize(event) {
        const target = event.target;
        let { x, y } = target.dataset;

        x = (parseFloat(x) || 0) + event.deltaRect.left;
        y = (parseFloat(y) || 0) + event.deltaRect.top;

        target.style.width = event.rect.width + "px";
        target.style.height = event.rect.height + "px";
        target.style.transform = `translate(${x}px, ${y}px)`;

        target.setAttribute("data-x", x);
        target.setAttribute("data-y", y);
    }

    handleSlotResizeEnd(event) {
        const target = event.target;
        const slotId = target.dataset.slotId;
        const slot = this.slots.find((s) => s.id === slotId);

        if (!slot) return;

        const newWidth = event.rect.width;
        const newDuration = Math.round(newWidth / this.cellWidth);

        let newStartHour, newEndHour;

        if (event.edges && event.edges.left) {
            // Left edge was moved - adjust start hour
            newEndHour = slot.endHour;
            newStartHour = newEndHour - newDuration;
        } else {
            // Right edge was moved - adjust end hour
            newStartHour = slot.startHour;
            newEndHour = newStartHour + newDuration;
        }

        // Check bounds and overlaps
        if (
            newStartHour >= 0 &&
            newEndHour <= 24 &&
            newEndHour > newStartHour &&
            !this.hasOverlap(slot.day, newStartHour, newEndHour, slotId)
        ) {
            const oldSlot = { ...slot };
            slot.startHour = newStartHour;
            slot.endHour = newEndHour;

            // Re-render slot with new size
            target.remove();
            this.renderSlot(slot);

            if (this.options.onSlotUpdate) {
                this.options.onSlotUpdate(slot, oldSlot);
            }
        } else {
            // Invalid resize, reset
            target.remove();
            this.renderSlot(slot);
            this.showMessage(this.labels.msgInvalidResize, "error");
        }
    }

    resetSlotPosition(target) {
        target.style.transform = "";
        target.removeAttribute("data-x");
        target.removeAttribute("data-y");
    }

    getDayFromY(y) {
        const dayRows = this.container.querySelectorAll(".day-row");
        for (let row of dayRows) {
            const rect = row
                .querySelector(".day-label")
                .getBoundingClientRect();
            const gridRect = this.container
                .querySelector("#schedule-grid")
                .getBoundingClientRect();
            const relativeTop = rect.top - gridRect.top;
            const relativeBottom = rect.bottom - gridRect.top;

            if (y >= relativeTop && y <= relativeBottom) {
                return row.dataset.day;
            }
        }
        return null;
    }

    getHourFromX(x) {
        const hour = Math.floor(x / this.cellWidth);
        return hour >= 0 && hour < 24 ? hour : null;
    }

    updateDropZones(clientX, clientY) {
        this.clearDropZones();

        const element = document.elementFromPoint(clientX, clientY);
        if (element && element.classList.contains("time-cell")) {
            element.classList.add("drop-zone");
        }
    }

    clearDropZones() {
        this.container
            .querySelectorAll(".drop-zone, .invalid-drop")
            .forEach((el) => {
                el.classList.remove("drop-zone", "invalid-drop");
            });
    }

    hasOverlap(day, startHour, endHour, excludeSlotId = null) {
        return this.slots.some((slot) => {
            if (slot.id === excludeSlotId) return false;
            if (slot.day !== day) return false;

            return !(endHour <= slot.startHour || startHour >= slot.endHour);
        });
    }

    deleteSlot(slotId) {
        const slotIndex = this.slots.findIndex((slot) => slot.id === slotId);
        if (slotIndex === -1) return;

        const slot = this.slots[slotIndex];
        this.slots.splice(slotIndex, 1);

        const slotElement = this.container.querySelector(
            `[data-slot-id="${slotId}"]`
        );
        if (slotElement) {
            slotElement.remove();
        }

        if (this.options.onSlotDelete) {
            this.options.onSlotDelete(slot);
        }
    }

    setBusinessHours() {
        this.clearAll();

        this.options.businessDays.forEach((day) => {
            const slot = {
                id: `slot-${this.slotIdCounter++}`,
                day: day,
                startHour: this.options.businessHours.start,
                endHour: this.options.businessHours.end,
            };
            this.slots.push(slot);
            this.renderSlot(slot);
        });

        this.showMessage(this.labels.msgBusinessApplied, "success");
    }

    clearAll() {
        this.slots = [];
        this.container
            .querySelectorAll(".time-slot")
            .forEach((slot) => slot.remove());
        this.showMessage(this.labels.msgCleared, "info");
    }

    saveSchedule() {
        const scheduleData = this.getScheduleData();

        if (this.options.onSave) {
            this.options.onSave(scheduleData);
        } else {
            console.log("Schedule data:", scheduleData);
            this.showMessage(this.labels.msgSaved, "success");
        }
    }

    getScheduleData() {
        return this.slots.map((slot) => ({
            day: slot.day,
            startHour: slot.startHour,
            endHour: slot.endHour,
            startTime: `${slot.startHour.toString().padStart(2, "0")}:00`,
            endTime: `${slot.endHour.toString().padStart(2, "0")}:00`,
        }));
    }

    loadData(data) {
        this.clearAll();

        data.forEach((item) => {
            const slot = {
                id: `slot-${this.slotIdCounter++}`,
                day: item.day,
                startHour: item.startHour || this.parseTime(item.startTime),
                endHour: item.endHour || this.parseTime(item.endTime),
            };

            if (!this.hasOverlap(slot.day, slot.startHour, slot.endHour)) {
                this.slots.push(slot);
                this.renderSlot(slot);
            }
        });
    }

    parseTime(timeString) {
        if (typeof timeString === "number") return timeString;
        const [hours] = timeString.split(":");
        return parseInt(hours, 10);
    }

    setupInteractions() {
        // Setup interact.js for the grid
        interact(this.container.querySelector(".schedule-grid")).dropzone({
            accept: ".time-slot",
            ondrop: (event) => {
                // Handle drop events
            },
        });
    }

    showMessage(message, type = "info") {
        console.log(`[${type.toUpperCase()}] ${message}`);

        if (type === "error") {
            alert(message);
        }
    }

    capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // Public API methods
    destroy() {
        if (this.container) {
            this.container.innerHTML = "";
        }
    }

    refresh() {
        this.calculateCellWidth();
        // Re-render all slots
        const currentSlots = [...this.slots];
        this.container
            .querySelectorAll(".time-slot")
            .forEach((slot) => slot.remove());
        currentSlots.forEach((slot) => this.renderSlot(slot));
    }

    adjustSlotFontSize(slotElement, width) {
        let fontSize;
        if (width < 60) {
            fontSize = "0.5rem";
        } else if (width < 120) {
            fontSize = "0.65rem";
        } else if (width < 180) {
            fontSize = "0.75rem";
        } else {
            fontSize = "0.875rem";
        }

        const textSpan = slotElement.querySelector("span");
        if (textSpan) {
            textSpan.style.fontSize = fontSize;
        }
    }
}

if (typeof module !== "undefined" && module.exports) {
    module.exports = InteractiveScheduler;
}

if (typeof window !== "undefined") {
    window.InteractiveScheduler = InteractiveScheduler;
}
