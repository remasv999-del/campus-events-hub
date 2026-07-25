/* Campus Events Hub - Front-end JavaScript */

const events = [
  {
    id: 1,
    title: "Web Development Workshop",
    category: "Workshop",
    date: "2026-07-28",
    time: "10:00 AM",
    location: "Computer Lab 2",
    shortDescription: "Learn the foundations of semantic HTML, modern CSS, and responsive design.",
    description: "This practical workshop introduces semantic HTML5, shared CSS styling, responsive layouts, and basic accessibility. Students will build a simple landing page and test it on different screen sizes.",
    image: "assets/images/web-workshop.svg"
  },
  {
    id: 2,
    title: "Cybersecurity Awareness Seminar",
    category: "Seminar",
    date: "2026-07-30",
    time: "12:30 PM",
    location: "Main Auditorium",
    shortDescription: "Understand common online threats and practical methods for protecting personal data.",
    description: "The seminar covers phishing, password security, social engineering, safe browsing, and responsible use of university systems. It includes examples and a short question-and-answer session.",
    image: "assets/images/cybersecurity.svg"
  },
  {
    id: 3,
    title: "Programming Competition",
    category: "Competition",
    date: "2026-08-02",
    time: "9:00 AM",
    location: "Innovation Center",
    shortDescription: "Solve programming challenges in teams and compete for the highest score.",
    description: "Students will work in small teams to solve a set of programming problems within a limited time. The competition evaluates problem solving, teamwork, code correctness, and time management.",
    image: "assets/images/programming.svg"
  },
  {
    id: 4,
    title: "Artificial Intelligence Workshop",
    category: "Workshop",
    date: "2026-08-04",
    time: "11:00 AM",
    location: "Smart Classroom 1",
    shortDescription: "Explore basic AI concepts and responsible uses of machine learning tools.",
    description: "This introductory session explains artificial intelligence, machine learning, training data, model limitations, and responsible use. Students will discuss practical examples from education and healthcare.",
    image: "assets/images/ai-workshop.svg"
  },
  {
    id: 5,
    title: "Technology Company Trip",
    category: "Trip",
    date: "2026-08-06",
    time: "8:00 AM",
    location: "University Main Gate",
    shortDescription: "Visit a local technology company and learn about real development teams.",
    description: "The trip gives students an opportunity to observe a professional technology workplace, meet software specialists, and learn how development projects are planned, tested, and delivered.",
    image: "assets/images/tech-trip.svg"
  },
  {
    id: 6,
    title: "Mobile Application Challenge",
    category: "Competition",
    date: "2026-08-08",
    time: "1:00 PM",
    location: "Student Activity Hall",
    shortDescription: "Present a mobile application idea and receive feedback from a judging panel.",
    description: "Participants will present a mobile application concept that solves a student or community problem. Each team will explain the target users, main features, interface design, and expected value.",
    image: "assets/images/mobile-challenge.svg"
  }
];

document.addEventListener("DOMContentLoaded", () => {
  setupNavigation();
  updateYear();

  const page = document.body.dataset.page;

  if (page === "home") renderUpcomingEvents();
  if (page === "events") setupEventsPage();
  if (page === "event-details") renderEventDetails();
  if (page === "register") setupRegistrationForm();
  if (page === "registrations") renderRegistrations();
  if (page === "about") setupContactForm();
});

function setupNavigation() {
  const button = document.querySelector(".menu-toggle");
  const navigation = document.querySelector(".main-nav");

  if (!button || !navigation) return;

  button.addEventListener("click", () => {
    const isOpen = navigation.classList.toggle("open");
    button.setAttribute("aria-expanded", String(isOpen));
  });
}

function updateYear() {
  document.querySelectorAll(".current-year").forEach((element) => {
    element.textContent = new Date().getFullYear();
  });
}

function formatDate(dateString) {
  const date = new Date(`${dateString}T00:00:00`);
  return new Intl.DateTimeFormat("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric"
  }).format(date);
}

function createEventCard(event) {
  return `
    <article class="event-card">
      <img src="${event.image}" alt="${escapeHtml(event.title)}">
      <div class="event-card-content">
        <span class="badge">${escapeHtml(event.category)}</span>
        <h3>${escapeHtml(event.title)}</h3>
        <div class="event-meta">
          <span><strong>Date:</strong> ${formatDate(event.date)}</span>
          <span><strong>Time:</strong> ${escapeHtml(event.time)}</span>
          <span><strong>Location:</strong> ${escapeHtml(event.location)}</span>
        </div>
        <p class="event-description">${escapeHtml(event.shortDescription)}</p>
        <a class="button primary" href="event.html?id=${event.id}">View Details</a>
      </div>
    </article>
  `;
}

function renderUpcomingEvents() {
  const container = document.querySelector("#upcoming-events");
  if (!container) return;

  const upcoming = [...events]
    .sort((a, b) => new Date(a.date) - new Date(b.date))
    .slice(0, 3);

  container.innerHTML = upcoming.map(createEventCard).join("");
}

function setupEventsPage() {
  const container = document.querySelector("#events-list");
  const searchInput = document.querySelector("#event-search");
  const categorySelect = document.querySelector("#category-filter");
  const countElement = document.querySelector("#event-count");

  if (!container || !searchInput || !categorySelect || !countElement) return;

  function updateList() {
    const searchTerm = searchInput.value.trim().toLowerCase();
    const selectedCategory = categorySelect.value;

    const filtered = events.filter((event) => {
      const matchesSearch =
        event.title.toLowerCase().includes(searchTerm) ||
        event.location.toLowerCase().includes(searchTerm);

      const matchesCategory =
        selectedCategory === "all" || event.category === selectedCategory;

      return matchesSearch && matchesCategory;
    });

    countElement.textContent = `${filtered.length} event${filtered.length === 1 ? "" : "s"} found`;

    if (filtered.length === 0) {
      container.innerHTML = `
        <div class="empty-state">
          <h2>No matching events</h2>
          <p>Try changing the search text or category.</p>
        </div>
      `;
      return;
    }

    container.innerHTML = filtered.map(createEventCard).join("");
  }

  searchInput.addEventListener("input", updateList);
  categorySelect.addEventListener("change", updateList);
  updateList();
}

function renderEventDetails() {
  const container = document.querySelector("#event-details");
  if (!container) return;

  const params = new URLSearchParams(window.location.search);
  const eventId = Number(params.get("id"));
  const selectedEvent = events.find((event) => event.id === eventId);

  if (!selectedEvent) {
    container.innerHTML = `
      <div class="empty-state">
        <h1>Event Not Found</h1>
        <p>The requested event does not exist or the event ID is invalid.</p>
        <a class="button primary" href="events.html">Browse Events</a>
      </div>
    `;
    return;
  }

  document.title = `Campus Events Hub | ${selectedEvent.title}`;

  container.innerHTML = `
    <img src="${selectedEvent.image}" alt="${escapeHtml(selectedEvent.title)}">
    <div>
      <span class="badge">${escapeHtml(selectedEvent.category)}</span>
      <h1>${escapeHtml(selectedEvent.title)}</h1>
      <p>${escapeHtml(selectedEvent.description)}</p>
      <ul class="details-list">
        <li><strong>Date:</strong> ${formatDate(selectedEvent.date)}</li>
        <li><strong>Time:</strong> ${escapeHtml(selectedEvent.time)}</li>
        <li><strong>Location:</strong> ${escapeHtml(selectedEvent.location)}</li>
      </ul>
      <a class="button primary" href="register.html?event=${selectedEvent.id}">Register for This Event</a>
    </div>
  `;
}

function setupRegistrationForm() {
  const form = document.querySelector("#registration-form");
  const eventSelect = document.querySelector("#selected-event");
  const message = document.querySelector("#registration-message");

  if (!form || !eventSelect || !message) return;

  events.forEach((event) => {
    const option = document.createElement("option");
    option.value = String(event.id);
    option.textContent = `${event.title} — ${formatDate(event.date)}`;
    eventSelect.appendChild(option);
  });

  const params = new URLSearchParams(window.location.search);
  const preselectedEvent = params.get("event");

  if (preselectedEvent && events.some((event) => String(event.id) === preselectedEvent)) {
    eventSelect.value = preselectedEvent;
  }

  form.addEventListener("submit", (submitEvent) => {
    submitEvent.preventDefault();
    clearFormErrors(form);
    message.className = "form-message";
    message.textContent = "";

    const formData = new FormData(form);
    const values = {
      fullName: String(formData.get("fullName") || "").trim(),
      studentId: String(formData.get("studentId") || "").trim(),
      email: String(formData.get("email") || "").trim(),
      eventId: String(formData.get("eventId") || "").trim(),
      agreement: formData.get("agreement") === "on"
    };

    const errors = validateRegistration(values);

    if (Object.keys(errors).length > 0) {
      displayFormErrors(form, errors);
      message.className = "form-message error";
      message.textContent = "Please correct the highlighted fields.";
      return;
    }

    const selectedEvent = events.find((event) => String(event.id) === values.eventId);
    const registrations = getRegistrations();

    registrations.push({
      id: Date.now(),
      fullName: values.fullName,
      studentId: values.studentId,
      email: values.email,
      eventId: Number(values.eventId),
      eventTitle: selectedEvent ? selectedEvent.title : "Unknown Event",
      registeredAt: new Date().toLocaleString("en-US")
    });

    localStorage.setItem("campusEventRegistrations", JSON.stringify(registrations));

    form.reset();
    message.className = "form-message success";
    message.textContent = "Registration submitted successfully.";
    message.scrollIntoView({ behavior: "smooth", block: "center" });
  });
}

function validateRegistration(values) {
  const errors = {};

  if (values.fullName.length < 3) {
    errors.fullName = "Enter a full name containing at least 3 characters.";
  }

  if (!/^\d{6,12}$/.test(values.studentId)) {
    errors.studentId = "Student ID must contain 6 to 12 digits.";
  }

  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(values.email)) {
    errors.email = "Enter a valid email address.";
  }

  if (!events.some((event) => String(event.id) === values.eventId)) {
    errors.eventId = "Select a valid event.";
  }

  if (!values.agreement) {
    errors.agreement = "You must confirm the information.";
  }

  return errors;
}

function getRegistrations() {
  try {
    const stored = localStorage.getItem("campusEventRegistrations");
    return stored ? JSON.parse(stored) : [];
  } catch (error) {
    console.error("Could not read registrations:", error);
    return [];
  }
}

function renderRegistrations() {
  const tableBody = document.querySelector("#registrations-table-body");
  const countElement = document.querySelector("#registration-count");
  const tableWrapper = document.querySelector(".table-wrapper");
  const emptyState = document.querySelector("#empty-registrations");
  const clearButton = document.querySelector("#clear-registrations");

  if (!tableBody || !countElement || !tableWrapper || !emptyState || !clearButton) return;

  function updateTable() {
    const registrations = getRegistrations();
    countElement.textContent = `${registrations.length} registration${registrations.length === 1 ? "" : "s"}`;

    if (registrations.length === 0) {
      tableWrapper.hidden = true;
      emptyState.hidden = false;
      clearButton.hidden = true;
      tableBody.innerHTML = "";
      return;
    }

    tableWrapper.hidden = false;
    emptyState.hidden = true;
    clearButton.hidden = false;

    tableBody.innerHTML = registrations.map((registration) => `
      <tr>
        <td>${escapeHtml(registration.fullName)}</td>
        <td>${escapeHtml(registration.studentId)}</td>
        <td>${escapeHtml(registration.email)}</td>
        <td>${escapeHtml(registration.eventTitle)}</td>
        <td>${escapeHtml(registration.registeredAt)}</td>
      </tr>
    `).join("");
  }

  clearButton.addEventListener("click", () => {
    const confirmed = window.confirm("Are you sure you want to remove all registrations?");
    if (!confirmed) return;

    localStorage.removeItem("campusEventRegistrations");
    updateTable();
  });

  updateTable();
}

function setupContactForm() {
  const form = document.querySelector("#contact-form");
  const message = document.querySelector("#contact-message");

  if (!form || !message) return;

  form.addEventListener("submit", (submitEvent) => {
    submitEvent.preventDefault();
    clearFormErrors(form);

    const formData = new FormData(form);
    const values = {
      contactName: String(formData.get("contactName") || "").trim(),
      contactEmail: String(formData.get("contactEmail") || "").trim(),
      contactSubject: String(formData.get("contactSubject") || "").trim(),
      contactText: String(formData.get("contactText") || "").trim()
    };

    const errors = {};

    if (values.contactName.length < 3) {
      errors.contactName = "Enter a valid name.";
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(values.contactEmail)) {
      errors.contactEmail = "Enter a valid email address.";
    }

    if (values.contactSubject.length < 4) {
      errors.contactSubject = "The subject must contain at least 4 characters.";
    }

    if (values.contactText.length < 15) {
      errors.contactText = "The message must contain at least 15 characters.";
    }

    if (Object.keys(errors).length > 0) {
      displayFormErrors(form, errors);
      message.className = "form-message error";
      message.textContent = "Please correct the highlighted fields.";
      return;
    }

    message.className = "form-message success";
    message.textContent = "The contact form is valid. No email was sent.";
    form.reset();
  });
}

function clearFormErrors(form) {
  form.querySelectorAll(".error-message").forEach((element) => {
    element.textContent = "";
  });
}

function displayFormErrors(form, errors) {
  Object.entries(errors).forEach(([field, text]) => {
    const errorElement = form.querySelector(`[data-error-for="${field}"]`);
    if (errorElement) errorElement.textContent = text;
  });
}

function escapeHtml(value) {
  return String(value)
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}
