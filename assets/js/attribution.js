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

    /*
     * Capture attribution on page load
     */
    (function captureAttribution() {

        const params = new URLSearchParams(window.location.search);

        const attribution = {
            utm_source:
                params.get("utm_source") || "seo",

            utm_medium:
                params.get("utm_medium") || "ggo",

            utm_campaign:
                params.get("utm_campaign") ||
                "2026_q2_fam_own_lfc_org_seo_ggo_fam-games-sub-seo",

            utm_term:
                params.get("utm_term") || "",

            utm_content:
                params.get("utm_content") || "",

            affiliate_id:
                params.get("affiliate_id") || ""
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

            if (value && !attribution.click_id) {

                attribution.click_field = field;
                attribution.click_id = value;

                if (
                    field === "gclid" ||
                    field === "gbraid" ||
                    field === "wbraid"
                ) {
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

        Object.keys(attribution).forEach(function (key) {
            if (attribution[key]) {
                setCookie(key, attribution[key]);
            }
        });

        saveAttribution(attribution);

    })();

    /*
     * Submit phone
     */
    window.submitPhone = function () {

        const phoneInput =
            document.getElementById("phoneInput");

        const termsCheck =
            document.getElementById("termsCheck");

        if (!phoneInput || !termsCheck) {
            alert("Form not ready");
            return;
        }

        if (!termsCheck.checked) {
            alert("You must agree to the Terms");
            return;
        }

        let phone = phoneInput.value.trim();

        if (!phone) {
            alert("Enter phone number");
            phoneInput.focus();
            return;
        }

        phone = phone.replace(/\D/g, "");

        if (
            phone.startsWith("639") &&
            phone.length === 12
        ) {
            phone = "0" + phone.slice(2);

        } else if (
            phone.startsWith("63") &&
            phone.length === 12
        ) {
            phone = "0" + phone.slice(2);

        } else if (
            phone.startsWith("9") &&
            phone.length === 10
        ) {
            phone = "0" + phone;

        } else if (
            !(
                phone.startsWith("09") &&
                phone.length === 11
            )
        ) {
            alert("Invalid PH number");
            phoneInput.focus();
            return;
        }

        const attribution = getAttribution();

        const query = new URLSearchParams();

        query.set("mobile", phone);

        query.set(
            "utm_source",
            attribution.utm_source || "seo"
        );

        query.set(
            "utm_medium",
            attribution.utm_medium || "ggo"
        );

        query.set(
            "utm_campaign",
            attribution.utm_campaign ||
            "2026_q2_fam_own_lfc_org_seo_ggo_fam-games-sub-seo-reg-bonus"
        );

        if (attribution.utm_term) {
            query.set(
                "utm_term",
                attribution.utm_term
            );
        }

        if (attribution.utm_content) {
            query.set(
                "utm_content",
                attribution.utm_content
            );
        }

        if (attribution.affiliate_id) {
            query.set(
                "affiliate_id",
                attribution.affiliate_id
            );
        }

        if (
            attribution.click_field &&
            attribution.click_id
        ) {
            query.set(
                "click_field",
                attribution.click_field
            );

            query.set(
                "click_id",
                attribution.click_id
            );

            query.set(
                "click_platform",
                attribution.click_platform
            );
        }

        window.location.href =
            "https://funalomax.com/en/profile/wallet?tab=deposit&" +
            query.toString();
    };

    /*
     * Register redirect — for .fm-register-btn elements (no phone field).
     * Reuses the captured attribution and sends mobile=true.
     */
    window.goToRegister = function () {

        const attribution = getAttribution();

        const query = new URLSearchParams();

        query.set(
            "utm_source",
            attribution.utm_source || "seo"
        );

        query.set(
            "utm_medium",
            attribution.utm_medium || "ggo"
        );

        query.set(
            "utm_campaign",
            attribution.utm_campaign ||
            "2026_q2_fam_own_lfc_org_seo_ggo_fam-games-sub-seo"
        );

        if (attribution.utm_term) {
            query.set("utm_term", attribution.utm_term);
        }

        if (attribution.utm_content) {
            query.set("utm_content", attribution.utm_content);
        }

        if (attribution.affiliate_id) {
            query.set("affiliate_id", attribution.affiliate_id);
        }

        if (
            attribution.click_field &&
            attribution.click_id
        ) {
            query.set("click_field", attribution.click_field);
            query.set("click_id", attribution.click_id);
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

    /*
     * Normalize phone input
     */
    const phoneInput =
        document.getElementById("phoneInput");

    if (phoneInput) {
        phoneInput.addEventListener(
            "input",
            function () {

                let value =
                    this.value.replace(/\D/g, "");

                if (
                    value.startsWith("639")
                ) {
                    value =
                        "0" + value.slice(2);

                } else if (
                    value.startsWith("63")
                ) {
                    value =
                        "0" + value.slice(2);

                } else if (
                    value.startsWith("9")
                ) {
                    value =
                        "0" + value;
                }

                this.value = value;
            }
        );
    }

});
