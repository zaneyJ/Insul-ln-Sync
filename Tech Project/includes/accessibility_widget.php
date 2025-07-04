<?php
// This file contains the HTML structure for the accessibility widget
?>
<button id="accessibility-toggle">♿ Accessibility</button>

<div id="accessibility-panel">
    <button class="accessibility-button" onclick="changeTextSize(10)">A+ Increase Text</button>
    <button class="accessibility-button" onclick="changeTextSize(-10)">A- Decrease Text</button>
    <button class="accessibility-button" onclick="toggleContrast()">Toggle High Contrast</button>
    <button class="accessibility-button" id="tts-toggle">🔊 Toggle Text-to-Speech</button>
</div>

<div id="error-message" class="error-message"></div> 