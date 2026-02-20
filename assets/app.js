function loadPage(page) {
    fetch(page)
    .then(response => response.text())
    .then(data => {
        document.getElementById("content").innerHTML = data;
    })
    .catch(err => {
        document.getElementById("content").innerHTML = "Error loading page.";
    });
}
