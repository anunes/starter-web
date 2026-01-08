(function () {
  const storageKey = "theme";
  const getStoredTheme = () => {
    try {
      return localStorage.getItem(storageKey);
    } catch (err) {
      return null;
    }
  };
  const getPreferredTheme = () => {
    const storedTheme = getStoredTheme();
    if (storedTheme === "light" || storedTheme === "dark") {
      return storedTheme;
    }
    const prefersDark =
      window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches;
    return prefersDark ? "dark" : "light";
  };
  const applyTheme = (theme) => {
    document.documentElement.setAttribute("data-bs-theme", theme);
  };

  applyTheme(getPreferredTheme());

  document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.getElementById("themeToggle");
    const icon = document.getElementById("themeIcon");
    if (!toggle) {
      return;
    }

    const setToggleLabel = (theme) => {
      const lightLabel = toggle.dataset.labelLight || toggle.getAttribute("title");
      const darkLabel = toggle.dataset.labelDark || toggle.getAttribute("title");
      const label = theme === "dark" ? darkLabel : lightLabel;
      if (label) {
        toggle.setAttribute("title", label);
        toggle.setAttribute("aria-label", label);
      }
    };

    const refreshTooltip = () => {
      if (!window.bootstrap || !toggle || !toggle.dataset.bsToggle) {
        return;
      }
      if (bootstrap.Tooltip) {
        const instance = bootstrap.Tooltip.getInstance(toggle);
        if (instance) {
          instance.dispose();
        }
        new bootstrap.Tooltip(toggle);
      }
    };

    const setTheme = (theme) => {
      applyTheme(theme);
      toggle.setAttribute("aria-pressed", theme === "dark");
      setToggleLabel(theme);
      if (icon) {
        icon.classList.remove("bi-sun-fill", "bi-moon");
        icon.classList.add(theme === "dark" ? "bi-sun-fill" : "bi-moon");
      }
      refreshTooltip();
    };

    setTheme(document.documentElement.getAttribute("data-bs-theme") || getPreferredTheme());

    toggle.addEventListener("click", function () {
      const currentTheme = document.documentElement.getAttribute("data-bs-theme") || "light";
      const nextTheme = currentTheme === "dark" ? "light" : "dark";
      setTheme(nextTheme);
      try {
        localStorage.setItem(storageKey, nextTheme);
      } catch (err) {
        // Ignore storage errors (e.g., private mode).
      }
    });
  });
})();
