$(document).ready(function () {
  $(".flash-message").delay(5000).slideUp(300);

  const getTooltipPlacement = (element) => {
    const pointer = element._tooltipPointer;
    if (!pointer) {
      return "top";
    }

    const rect = element.getBoundingClientRect();
    const centerX = rect.left + rect.width / 2;
    const centerY = rect.top + rect.height / 2;
    const dx = pointer.x - centerX;
    const dy = pointer.y - centerY;

    if (dx === 0 && dy === 0) {
      return "top";
    }

    if (Math.abs(dx) > Math.abs(dy)) {
      return dx > 0 ? "left" : "right";
    }

    return dy > 0 ? "top" : "bottom";
  };

  const initTooltips = () => {
    const tooltipElements = document.querySelectorAll(
      '[data-bs-toggle="tooltip"]'
    );
    tooltipElements.forEach((element) => {
      if (element._tooltipBound) {
        return;
      }

      const recordPointer = (event) => {
        if (typeof event.clientX !== "number") {
          return;
        }
        element._tooltipPointer = { x: event.clientX, y: event.clientY };
      };

      element.addEventListener("mouseenter", recordPointer);
      element.addEventListener("mousemove", recordPointer);
      element.addEventListener("focus", () => {
        element._tooltipPointer = null;
      });

      element._tooltipBound = true;
      element._tooltipInstance = new bootstrap.Tooltip(element, {
        placement: () => getTooltipPlacement(element),
      });
    });
  };

  initTooltips();

  let appIconsInput = document.getElementById("app_icons_color");
  let appIconsPicker = document.getElementById("app_icons_color_picker");
  const bindAppIconsPicker = () => {
    if (!appIconsInput || !appIconsPicker) {
      return;
    }

    if (!appIconsInput.dataset.colorSyncBound) {
      appIconsInput.addEventListener("input", function () {
        if (/^#([A-Fa-f0-9]{6})$/.test(appIconsInput.value)) {
          appIconsPicker.value = appIconsInput.value;
        }
      });
      appIconsInput.dataset.colorSyncBound = "true";
    }

    appIconsPicker.addEventListener("input", function () {
      appIconsInput.value = appIconsPicker.value;
    });

    appIconsPicker.addEventListener("change", function () {
      appIconsInput.value = appIconsPicker.value;
      const parent = appIconsPicker.parentNode;
      if (!parent) {
        return;
      }
      const replacement = appIconsPicker.cloneNode(true);
      replacement.value = appIconsPicker.value;
      parent.replaceChild(replacement, appIconsPicker);
      appIconsPicker = replacement;
      bindAppIconsPicker();
      initTooltips();
    });
  };

  bindAppIconsPicker();

  //Navbar active menu
  $(".navbar li a").each(function () {
    if ($(this).prop("href") == window.location.href) {
      $(this).addClass("active");
    }
  });

  $("#regname").on("input", function () {
    this.value = this.value.replace(/[^a-z]/g, "");
  });


  const dataTableOptions = {};
  const pageLang = document.documentElement.getAttribute("lang") || "en";
  if (pageLang.toLowerCase().startsWith("pt")) {
    dataTableOptions.language = { url: "/assets/json/pt-PT.json" };
  }
  new DataTable(".user-table", dataTableOptions);
});
