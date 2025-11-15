const searchInput = document.querySelector(".search-input input");
const bookItems = document.querySelectorAll(".library-body .book-body");
const selectDisplay = document.querySelector("#select .select-dropdown span");
const libraryBody = document.querySelector(".library-body");

// Create a reusable "Not Found" element (hidden by default)
const notFoundDiv = document.createElement("div");
notFoundDiv.classList.add("notfound-container");
notFoundDiv.style.display = "none";
notFoundDiv.innerHTML = `
    <h1>No Results</h1>
    <img src="./Assets/Images/dribbble_1.gif" alt="404 Not Found">
    <h2>Book Not Found</h2>
    <p>Sorry, the book you are looking for does not exist.</p>
`;
libraryBody.appendChild(notFoundDiv); 

function performSearch() {
  if (!bookItems.length) return;

  const raw = searchInput?.value?.trim().toLowerCase() || "";
  const filter = selectDisplay?.textContent?.trim().toLowerCase() || "all";

  let matchCount = 0;

  bookItems.forEach((item) => {
    const title =
      item.querySelector(".book-info h4")?.textContent?.toLowerCase() || "";
    const author =
      item.querySelector(".book-info p")?.textContent?.toLowerCase() || "";
    const category =
      item.querySelector(".category")?.textContent?.toLowerCase() || "";
    const fullText = item.textContent?.toLowerCase() || "";

    let matched = false;

    if (!raw) matched = true;
    else if (filter === "all")
      matched =
        title.includes(raw) ||
        author.includes(raw) ||
        category.includes(raw) ||
        fullText.includes(raw);
    else if (filter === "title") matched = title.includes(raw);
    else if (filter === "author") matched = author.includes(raw);
    else if (filter === "subject") matched = category.includes(raw);
    else if (filter === "text") matched = fullText.includes(raw);

    item.style.display = matched ? "grid" : "none";

    if (matched) matchCount++;
  });

  if (matchCount === 0) {
    notFoundDiv.style.display = "block";
  } else {
    notFoundDiv.style.display = "none";
  }
}

// EVENTS
searchInput?.addEventListener("input", performSearch);
searchInput?.addEventListener("keydown", (e) => {
  if (e.key === "Enter") {
    e.preventDefault();
    performSearch();
  }
});

document
  .querySelectorAll("#select .options-list .options")
  .forEach((opt) => opt.addEventListener("click", () => setTimeout(performSearch, 0)));

document.addEventListener("DOMContentLoaded", performSearch);


function wirePreviewButtons() {
  document.querySelectorAll(".preview").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      window.location.href = "./bookdetails.html";
    });
  });
}

document.addEventListener("DOMContentLoaded", wirePreviewButtons);

document.querySelectorAll(".favorite i").forEach((icon) => {
  icon.addEventListener("click", () => {
    icon.style.color = icon.style.color === "red" ? "grey" : "red";
  });
});
