<!-- Accessibility Toolbar -->
<div class="d-flex justify-content-end mb-3" aria-label="Accessibility Controls">
    <button class="btn btn-light btn-sm mr-2" id="increaseText" aria-label="Increase text size">
        <i class="fas fa-search-plus"></i>
    </button>
    <button class="btn btn-light btn-sm mr-2" id="decreaseText" aria-label="Decrease text size">
        <i class="fas fa-search-minus"></i>
    </button>
    <button class="btn btn-light btn-sm mr-2" id="toggleContrast" aria-label="Toggle high contrast mode">
        <i class="fas fa-adjust"></i>
    </button>
    <button class="btn btn-light btn-sm" id="toggleFont" aria-label="Toggle dyslexia-friendly font">
        <i class="fas fa-font"></i>
    </button>
</div>

<!-- Sample Content Area -->
<div class="content-area">
    <h2>Legal Advisory: Data Protection in Employment Contracts</h2>
    <p>
        Employers must obtain consent before processing sensitive employee information and must implement safeguards for data security.
    </p>
</div>

<!-- Accessibility CSS -->
<style>
    body.high-contrast {
        background-color: #000;
        color: #fff;
    }
    body.alt-font {
        font-family: "OpenDyslexic", Arial, sans-serif;
    }
</style>

<!-- Accessibility Script -->
<script>
    let fontSize = 100; // percentage

    document.getElementById("increaseText").addEventListener("click", () => {
        fontSize += 10;
        document.querySelector(".content-area").style.fontSize = fontSize + "%";
    });

    document.getElementById("decreaseText").addEventListener("click", () => {
        if (fontSize > 50) {
            fontSize -= 10;
            document.querySelector(".content-area").style.fontSize = fontSize + "%";
        }
    });

    document.getElementById("toggleContrast").addEventListener("click", () => {
        document.body.classList.toggle("high-contrast");
    });

    document.getElementById("toggleFont").addEventListener("click", () => {
        document.body.classList.toggle("alt-font");
    });
</script>
