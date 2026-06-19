document.addEventListener("DOMContentLoaded", function () {
    const DEFAULT_CAMPAIGN =
        "2026_q2_fam_own_lfc_org_seo_ggo_fam-games-sub-seo";

    const BONUS_CAMPAIGN =
        "2026_q2_fam_own_lfc_org_seo_ggo_fam-games-sub-seo-reg-bonus-offer";

    const STORAGE_KEY = "fm_attr";
    const LEGACY_STORAGE_KEY = "fm_attribution";

    const REDIRECT_BASE_URL =
        "https://funalomax.com/en/profile/wallet";

    let activeModalType = "register";

    const MODALS = {
        register: {
            modalId: "fm-reg-modal",
            closeId: "fm-reg-close",
            phoneSelector: ".phoneInput",
            termsSelector: "#fm-reg-terms, #termsCheck, .termsCheck",
            campaign: DEFAULT_CAMPAIGN,
            openSelector: ".fm-open-register, #fm-register-trigger",
            submitSelector: "#fm-reg-submit"
        },

        bonus: {
            modalId: "fm-welcome-modal",
            closeId: "fm-welcome-close",
            phoneSelector: ".phoneInput",
            termsSelector: null,
            campaign: BONUS_CAMPAIGN,
            openSelector:
                "#fnlmx-rg-proceed.fnlmax-play-bonus, #fnlmx-rg-proceed, .fnlmax-play-bonus",
            submitSelector: "#fm-welcome-submit"
        }
    };

    function parseJson(value) {
        try {
            return value ? JSON.parse(value) : {};
        } catch (error) {
            return {};
        }
    }

    function saveAttribution(data) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
    }

    function getAttribution() {
        const stored =
            localStorage.getItem(STORAGE_KEY) ||
            localStorage.getItem(LEGACY_STORAGE_KEY);

        return parseJson(stored);
    }

    function setCookie(name, value, days = 30) {
        if (!value) return;

        const expires = new Date(
            Date.now() + days * 86400000
        ).toUTCString();

        document.cookie =
            name +
            "=" +
            encodeURIComponent(value) +
            "; expires=" +
            expires +
            "; path=/; SameSite=Lax";
    }

    function syncCookies(attribution) {
        Object.keys(attribution).forEach(function (key) {
            if (attribution[key]) {
                setCookie(key, attribution[key]);
            }
        });
    }

    function captureAttribution() {
        const params = new URLSearchParams(window.location.search);
        const existingAttribution = getAttribution();

        const attribution = {
            utm_source:
                params.get("utm_source") ||
                existingAttribution.utm_source ||
                "seo",

            utm_medium:
                params.get("utm_medium") ||
                existingAttribution.utm_medium ||
                "ggo",

            utm_campaign:
                params.get("utm_campaign") ||
                existingAttribution.utm_campaign ||
                DEFAULT_CAMPAIGN,

            utm_term:
                params.get("utm_term") ||
                existingAttribution.utm_term ||
                "",

            utm_content:
                params.get("utm_content") ||
                existingAttribution.utm_content ||
                "",

            affiliate_id:
                params.get("affiliate_id") ||
                existingAttribution.affiliate_id ||
                ""
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

            if (!value || attribution.click_id) return;

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
        });

        if (!attribution.click_id && existingAttribution.click_id) {
            attribution.click_field = existingAttribution.click_field;
            attribution.click_id = existingAttribution.click_id;
            attribution.click_platform = existingAttribution.click_platform;
        }

        saveAttribution(attribution);
        syncCookies(attribution);
    }

    function setCampaignByModal(type) {
        const config = MODALS[type];

        if (!config) return;

        activeModalType = type;

        const attribution = getAttribution();

        attribution.utm_source = attribution.utm_source || "seo";
        attribution.utm_medium = attribution.utm_medium || "ggo";
        attribution.utm_campaign = config.campaign;

        saveAttribution(attribution);
        syncCookies(attribution);
    }

    function getModalElement(type) {
        const config = MODALS[type];

        if (!config) return null;

        return document.getElementById(config.modalId);
    }

    function getPhoneInput(type) {
        const config = MODALS[type];
        const modal = getModalElement(type);

        if (!config || !modal) return null;

        return modal.querySelector(config.phoneSelector);
    }

    function getTermsCheckbox(type) {
        const config = MODALS[type];
        const modal = getModalElement(type);

        if (!config || !modal || !config.termsSelector) return null;

        return modal.querySelector(config.termsSelector);
    }

    function toLocalPhone(value) {
        let phone = String(value || "").replace(/\D/g, "");

        /*
         * Accepts:
         * 09059631254
         * 9059631254
         * 639059631254
         * +639059631254
         */
        if (phone.startsWith("63") && phone.length >= 12) {
            phone = phone.slice(2);
        }

        if (phone.startsWith("0") && phone.length >= 11) {
            phone = phone.slice(1);
        }

        return phone;
    }

    function isValidLocalPhone(value) {
        return /^9\d{9}$/.test(value);
    }

    function normalizePhoneInput(input) {
        if (!input) return;

        /*
         * The field already shows the "+63" prefix, so the input should hold
         * only the 10-digit local number that starts with 9 (9XX XXX XXXX).
         *
         * - strip everything that isn't a digit
         * - drop a pasted "63" country code
         * - drop leading zeros so "0" can never be the first digit
         * - cap the length at 10 digits
         */
        let digits = String(input.value || "").replace(/\D/g, "");

        if (digits.startsWith("63")) {
            digits = digits.slice(2);
        }

        digits = digits.replace(/^0+/, "").slice(0, 10);

        input.value = digits;
    }

    function buildQuery(phone, attribution) {
        const query = new URLSearchParams();

        query.set("tab", "deposit");
        query.set("mobile", phone);

        query.set("utm_source", attribution.utm_source || "seo");
        query.set("utm_medium", attribution.utm_medium || "ggo");

        query.set(
            "utm_campaign",
            attribution.utm_campaign || DEFAULT_CAMPAIGN
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

        if (attribution.click_field && attribution.click_id) {
            query.set("click_field", attribution.click_field);
            query.set("click_id", attribution.click_id);

            if (attribution.click_platform) {
                query.set("click_platform", attribution.click_platform);
            }
        }

        return query;
    }

    window.submitPhone = function (type) {
        const modalType = type || activeModalType || "register";
        const config = MODALS[modalType];

        if (!config) return;

        setCampaignByModal(modalType);

        const phoneInput = getPhoneInput(modalType);
        const termsCheck = getTermsCheckbox(modalType);

        if (!phoneInput) {
            alert("Form not ready");
            return;
        }

        if (termsCheck && !termsCheck.checked) {
            alert("You must agree to the Terms");
            return;
        }

        const rawPhone = phoneInput.value;
        const localPhone = toLocalPhone(rawPhone);

        console.log("Modal type:", modalType);
        console.log("Raw phone:", rawPhone);
        console.log("Local phone:", localPhone);

        if (!isValidLocalPhone(localPhone)) {
            alert("Invalid PH number");
            phoneInput.focus();
            return;
        }

        const phone = "0" + localPhone;

        const attribution = getAttribution();
        const query = buildQuery(phone, attribution);

        window.location.href =
            REDIRECT_BASE_URL + "?" + query.toString();
    };

    function openModal(type) {
        const config = MODALS[type];
        const modal = getModalElement(type);

        if (!config || !modal) return;

        setCampaignByModal(type);

        const phoneInput = getPhoneInput(type);

        modal.classList.add("is-open");
        modal.setAttribute("aria-hidden", "false");
        document.body.classList.add("funalo-drawer-open");

        setTimeout(function () {
            if (phoneInput) {
                phoneInput.focus();
            }
        }, 150);
    }

    function closeModal(type) {
        const modal = getModalElement(type);

        if (modal) {
            modal.classList.remove("is-open");
            modal.setAttribute("aria-hidden", "true");
        }

        document.body.classList.remove("funalo-drawer-open");
    }

    function wirePhoneInput(type) {
        const phoneInput = getPhoneInput(type);

        if (!phoneInput) return;

        phoneInput.removeAttribute("maxlength");
        phoneInput.setAttribute("inputmode", "numeric");
        phoneInput.setAttribute("autocomplete", "tel");

        phoneInput.addEventListener("input", function () {
            normalizePhoneInput(this);
        });

        phoneInput.addEventListener("keydown", function (event) {
            if (event.key === "Enter") {
                event.preventDefault();
                window.submitPhone(type);
            }
        });
    }

    function wireCloseButton(type) {
        const config = MODALS[type];

        if (!config || !config.closeId) return;

        const closeButton = document.getElementById(config.closeId);

        if (!closeButton) return;

        closeButton.addEventListener("click", function (event) {
            event.preventDefault();
            closeModal(type);
        });
    }

    captureAttribution();

    document.addEventListener("click", function (event) {
        const registerTrigger = event.target.closest(
            MODALS.register.openSelector
        );

        if (registerTrigger) {
            event.preventDefault();
            openModal("register");
            return;
        }

        const bonusTrigger = event.target.closest(
            MODALS.bonus.openSelector
        );

        if (bonusTrigger) {
            event.preventDefault();
            openModal("bonus");
        }
    });

    document.addEventListener(
        "click",
        function (event) {
            const registerSubmit = event.target.closest(
                MODALS.register.submitSelector
            );

            if (registerSubmit) {
                event.preventDefault();
                event.stopPropagation();

                if (event.stopImmediatePropagation) {
                    event.stopImmediatePropagation();
                }

                window.submitPhone("register");
                return;
            }

            const bonusSubmit = event.target.closest(
                MODALS.bonus.submitSelector
            );

            if (bonusSubmit) {
                event.preventDefault();
                event.stopPropagation();

                if (event.stopImmediatePropagation) {
                    event.stopImmediatePropagation();
                }

                window.submitPhone("bonus");
            }
        },
        true
    );

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
            closeModal("register");
            closeModal("bonus");
            return;
        }

        if (
            (event.key === "Enter" || event.key === " ") &&
            event.target.closest("#fm-register-trigger")
        ) {
            event.preventDefault();
            openModal("register");
        }
    });

    Object.keys(MODALS).forEach(function (type) {
        wirePhoneInput(type);
        wireCloseButton(type);
    });
});