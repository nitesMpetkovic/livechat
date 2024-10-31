function sendMessage() {
    const message = document.getElementById('message').value;
    const recipientId = document.getElementById('recipient').value;

    if (!message) {
        alert("Molimo unesite poruku!");
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "send_message.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById('message').value = "";  // Očisti polje za unos poruke
            loadMessages();  // Osveži poruke
        }
    };
    xhr.send("message=" + encodeURIComponent(message) + "&recipient=" + encodeURIComponent(recipientId));
}

function loadMessages() {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "load_messages.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById('chat-box').innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}

// Pozivamo loadMessages da se učitaju poruke prilikom učitavanja stranice
window.onload = loadMessages;
