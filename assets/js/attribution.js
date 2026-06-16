/**
 * Attribution capture + "Register" redirect.
 *
 * Captures UTM / click-id params on every page load (persisted to localStorage
 * and cookies), then redirects to the registration URL with that attribution.
 *
 * Usage: add the class `fm-register-btn` to any <a> or <button>. Clicking it
 * runs goToRegister(). The global window.goToRegister() is also still callable
 * directly (e.g. from an inline onclick) if needed.
 */
document.addEventListener("DOMContentLoaded", function () {

    const STORAGE_KEY = "fm_attr";
    const REGISTER_CLASS = "fm-register-btn";

    function saveAttribution(data) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
    }

    function getAttribution() {
        const stored =
            localStorage.getItem(STORAGE_KEY) ||
            localStorage.getItem("fm_attribution");

        return stored ? JSON.parse(stored) : {};
    }

    function setCookie(name, value, days = 30) {
        const expires = new Date(
            Date.now() + days * 86400000
        ).toUTCString();

        document.cookie =
            `${name}=${encodeURIComponent(value)}; expires=${expires}; path=/; SameSite=Lax`;
    }

    (function captureAttribution() {

        const params = new URLSearchParams(window.location.search);

        const attribution = {
            utm_source: params.get("utm_source") || "seo",
            utm_medium: params.get("utm_medium") || "ggo",
            utm_campaign: params.get("utm_campaign") ||
                "2026_q2_fam_own_lfc_org_seo_ggo_fam-games-sub-seo",
            utm_term: params.get("utm_term") || "",
            utm_content: params.get("utm_content") || "",
            affiliate_id: params.get("affiliate_id") || "",
            captured_at: new Date().toISOString()
        };

        [
            "gclid",
            "gbraid",
            "wbraid",
            "fbclid",
            "ttclid",
            "msclkid"
        ].forEach(function (field) {

            const value = params.get(field);

            if (value && !attribution.click_platform) {

                attribution.click_field = field;
                attribution.click_id = value;

                if (field.startsWith("g")) {
                    attribution.click_platform = "google";
                } else if (field === "fbclid") {
                    attribution.click_platform = "facebook";
                } else if (field === "ttclid") {
                    attribution.click_platform = "tiktok";
                } else {
                    attribution.click_platform = "microsoft";
                }
            }
        });

        if (!attribution.click_platform) {
            attribution.click_platform = "direct";
        }

        Object.keys(attribution).forEach(function (key) {
            if (attribution[key]) {
                setCookie(key, attribution[key]);
            }
        });

        saveAttribution(attribution);

    })();

    window.goToRegister = function () {

        const attribution = getAttribution();

        const query = new URLSearchParams();

        query.set("mobile", "true");
        query.set("utm_source", "seo-sub");
        query.set("utm_medium", "seo-sub");

        if (attribution.click_field) {
            query.set("click_field", attribution.click_field);
        }

        if (attribution.click_id) {
            query.set("click_id", attribution.click_id);
        }

        if (attribution.click_platform) {
            query.set("click_platform", attribution.click_platform);
        }

        window.location.href =
            "https://funalomax.com/en?" + query.toString();
    };

    /* Bind every element carrying the register class. Delegated on document so
       it also covers buttons added to the DOM after load. */
    document.addEventListener("click", function (event) {
        const trigger = event.target.closest("." + REGISTER_CLASS);
        if (!trigger) {
            return;
        }
        event.preventDefault();
        window.goToRegister();
    });

});
