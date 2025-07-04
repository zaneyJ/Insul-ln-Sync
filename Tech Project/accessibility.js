// Initialize variables
let fontSize = 100;
let highContrast = false;
let ttsEnabled = false;
let speechSynthesis = window.speechSynthesis;

// DOM Elements
const toggleButton = document.getElementById("accessibility-toggle");
const panel = document.getElementById("accessibility-panel");
const ttsButton = document.getElementById("tts-toggle");
const errorMessage = document.getElementById("error-message");

// Show error message
function showError(message) {
    errorMessage.textContent = message;
    errorMessage.style.display = "block";
    setTimeout(() => {
        errorMessage.style.display = "none";
    }, 5000);
}

// Handle image loading errors
function handleImageError(img) {
    img.style.display = "none";
    showError(`Failed to load image: ${img.alt}`);
}

// Show/hide panel
toggleButton.addEventListener("click", function() {
    panel.style.display = panel.style.display === "block" ? "none" : "block";
});

// Adjust text size
function changeTextSize(delta) {
    try {
        fontSize = Math.min(200, Math.max(50, fontSize + delta));
        document.body.style.fontSize = fontSize + "%";
    } catch (error) {
        showError("Failed to change text size");
    }
}

// Toggle high contrast
function toggleContrast() {
    try {
        highContrast = !highContrast;
        document.body.style.backgroundColor = highContrast ? "#000" : "#f5f5f5";
        document.body.style.color = highContrast ? "#FFF700" : "#333";
    } catch (error) {
        showError("Failed to toggle contrast");
    }
}

// Toggle TTS
ttsButton.addEventListener("click", function() {
    try {
        ttsEnabled = !ttsEnabled;
        if (ttsEnabled) {
            if (!speechSynthesis) {
                showError("Text-to-Speech is not supported in your browser");
                ttsEnabled = false;
                return;
            }
            const testSpeech = new SpeechSynthesisUtterance("Text to speech enabled");
            speechSynthesis.speak(testSpeech);
        }
    } catch (error) {
        showError("Failed to toggle text-to-speech");
        ttsEnabled = false;
    }
});

// Speak selected text
document.addEventListener("mouseup", function() {
    if (!ttsEnabled || !speechSynthesis) return;
    
    try {
        const selection = window.getSelection().toString().trim();
        if (selection) {
            const speech = new SpeechSynthesisUtterance(selection);
            speechSynthesis.cancel();
            speechSynthesis.speak(speech);
        }
    } catch (error) {
        showError("Failed to speak selected text");
    }
});

// Speak image alt on click
document.querySelectorAll("img").forEach(img => {
    img.addEventListener("click", function() {
        if (!ttsEnabled || !speechSynthesis) return;
        
        try {
            if (img.alt) {
                const speech = new SpeechSynthesisUtterance(img.alt);
                speechSynthesis.cancel();
                speechSynthesis.speak(speech);
            }
        } catch (error) {
            showError("Failed to speak image description");
        }
    });
});

// Check for browser compatibility on load
window.addEventListener("load", function() {
    if (!speechSynthesis) {
        showError("Text-to-Speech is not supported in your browser");
        ttsButton.disabled = true;
    }
}); 