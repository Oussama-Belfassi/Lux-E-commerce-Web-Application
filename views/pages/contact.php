<script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/2.3.1/list.min.js"></script>

<form class="row g-3" action="" method="post">
    <div class="head">
        <h4>Contact Us</h4>
    </div>

    <div class="row">
        <div class="col">
            <label class="form-label">FirstName</label>
            <div class="input-group has-validation position-relative">
                <input type="text" name="Firstname"
                    placeholder="FirstName"
                    value="<?= htmlspecialchars($userData['Firstname'] ?? '') ?>"
                    class="form-control <?= isset($errors['Firstname']) ? 'is-invalid' : '' ?>">
                <?php if (isset($errors['Firstname'])): ?>
                    <?php foreach ($errors['Firstname'] as $error): ?>
                        <div class="invalid-feedback"><?= $error ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="col">
            <label class="form-label">LastName</label>
            <div class="input-group has-validation position-relative">
                <input type="text" name="Lastname"
                    placeholder="LastName"
                    value="<?= htmlspecialchars($userData['Lastname'] ?? '') ?>"
                    class="form-control <?= isset($errors['Lastname']) ? 'is-invalid' : '' ?>">
                <?php if (isset($errors['Lastname'])): ?>
                    <?php foreach ($errors['Lastname'] as $error): ?>
                        <div class="invalid-feedback"><?= $error ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="email">
        <label class="form-label">Email</label>
        <div class="input-group has-validation position-relative">
            <span class="input-group-text">@</span>
            <input type="email" name="email"
                placeholder="You@example.com"
                value="<?= htmlspecialchars($userData['email'] ?? '') ?>"
                class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>">
            <?php if (isset($errors['email'])): ?>
                <?php foreach ($errors['email'] as $error): ?>
                    <div class="invalid-feedback"><?= $error ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="email">
        <label class="form-label">Phone</label>
        <div class="input-group has-validation position-relative">
            <div class="pn-select" id="js_pn-select" style="--prefix-length: 2">
                <button class="pn-selected-prefix" aria-label="Select phonenumber prefix"
                    id="js_trigger-dropdown" tabindex="1" type="button">
                    <img class="pn-selected-prefix__flag" id="js_selected-flag"
                        src="https://flagpedia.net/data/flags/icon/36x27/nl.png" />
                    <svg class="pn-selected-prefix__icon" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="#081626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9" />
                    </svg>
                </button>
                <div class="pn-input">
                    <div class="pn-input__container">
                        <input class="pn-input__prefix" value="<?= htmlspecialchars($userData['phonenumber-prefix'] ?? '+31') ?>" type="text"
                            name="phonenumber-prefix" id="js_number-prefix" tabindex="-1" />
                        <input class="pn-input__phonenumber" id="js_input-phonenumber"
                            type="tel" name="phonenumber" pattern="\d*" value="<?= htmlspecialchars($userData['phonenumber'] ?? '') ?>"
                            placeholder=" " autocomplete="off" maxlength="15" tabindex="0" />
                        <?php if (isset($errors['phonenumber'])): ?>
                            <?php foreach ($errors['phonenumber'] as $error): ?>
                                <small class="pn-input__error"><?= $error ?></small>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <small class="pn-input__error">This is not a valid phone number</small>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="pn-dropdown" id="js_dropdown">
                    <div class="pn-search">
                        <svg class="pn-search__icon" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="#103155" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input placeholder="Search for countries" class="pn-search__input search"
                            type="search" id="js_search-input" autocomplete="off" />
                    </div>
                    <ul class="pn-list list" id="js_list"></ul>
                    <div class="pn-list-item pn-list-item--no-results" style="display: none"
                        id="js_no-results-found">No results found</div>
                </div>
            </div>
        </div>
    </div>
    <div class="pass">
        <label class="form-label">Message</label>
        <div class="form-floating">
            <textarea
                name="message"
                placeholder="Leave a comment here"
                id="floatingTextarea"
                class="form-control <?= isset($errors['message']) ? 'is-invalid' : '' ?>"><?= htmlspecialchars($userData['message'] ?? '') ?></textarea>
            <label for="floatingTextarea" style="z-index: 0;">Message</label>
            <?php if (isset($errors['message'])): ?>
                <?php foreach ($errors['message'] as $error): ?>
                    <div class="invalid-feedback"><?= $error ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="frame">
        <button class="custom-btn btn-3" type="submit"><span>Contact Us</span></button>
    </div>
</form>

<script>
    const selectContainer = document.getElementById("js_pn-select");
    const countrySearchInput = document.getElementById("js_search-input");
    const noResultListItem = document.getElementById("js_no-results-found");
    const dropdownTrigger = document.getElementById("js_trigger-dropdown");
    const phoneNumberInput = document.getElementById("js_input-phonenumber");
    const dropdownContainer = document.getElementById("js_dropdown");
    const selectedPrefix = document.getElementById("js_number-prefix");
    const selectedFlag = document.getElementById("js_selected-flag");
    const listContainer = document.getElementById("js_list");

    let countryList;

    const init = async (countries) => {

        const setNewSelected = (prefix, flag) => {
            selectedFlag.src = `https://flagpedia.net/data/flags/icon/36x27/${flag}.png`;
            selectedPrefix.value = `+${prefix}`;
            selectContainer.style.setProperty("--prefix-length", prefix.length);
        };

        const addSelectedModifier = (flag) => {
            const previousSelected = document.querySelector(".pn-list-item--selected");
            const newSelected = document.querySelector(`.pn-list-item[data-flag="${flag}"]`);
            if (previousSelected) previousSelected.classList.remove("pn-list-item--selected");
            if (newSelected) newSelected.classList.add("pn-list-item--selected");
        };

        const selectCountry = (e) => {
            const {
                flag,
                prefix
            } = e.target.closest("li").dataset;
            setNewSelected(prefix, flag);
            closeDropdown();
            addSelectedModifier(flag);
        };

        let countdown;

        const closeOnMouseLeave = () => {
            countdown = setTimeout(() => closeDropdown(), 2000);
        };
        const clearTimeOut = () => clearTimeout(countdown);

        const attachDropdownEvents = () => {
            dropdownContainer.addEventListener("mouseleave", closeOnMouseLeave);
            dropdownContainer.addEventListener("mouseenter", clearTimeOut);
        };

        const removeDropdownEvents = () => {
            clearTimeout(countdown);
            dropdownContainer.removeEventListener("mouseleave", closeOnMouseLeave);
            dropdownContainer.removeEventListener("mouseenter", clearTimeOut);
        };

        const closeDropdown = () => {
            selectContainer.classList.remove("pn-select--open");
            listContainer.scrollTop = 0;
            countrySearchInput.value = "";
            countryList.search();
            phoneNumberInput.focus();
            removeDropdownEvents();
        };

        const openDropdown = () => {
            selectContainer.classList.add("pn-select--open");
            attachDropdownEvents();
        };

        document.addEventListener("click", (e) => {
            if (
                e.target !== selectContainer &&
                !selectContainer.contains(e.target) &&
                selectContainer.classList.contains("pn-select--open")
            ) {
                closeDropdown();
            }
        });

        // FIX 7: single innerHTML assignment instead of += in loop
        const createList = () => new Promise((resolve) => {
            const items = countries.map(({
                name,
                prefix,
                flag
            }) => `
            <li class="pn-list-item ${flag === 'nl' ? 'pn-list-item--selected' : ''} js_pn-list-item"
                data-flag="${flag}" data-prefix="${prefix}"
                tabindex="0" role="button" aria-pressed="false">
                <img class="pn-list-item__flag"
                    src="https://flagpedia.net/data/flags/icon/36x27/${flag}.png" />
                <span class="pn-list-item__country js_country-name">${name}</span>
                <span class="pn-list-item__prefix js_country-prefix">(+${prefix})</span>
            </li>`);
            listContainer.innerHTML = items.join('');
            resolve();
        });

        const attachListItemEventListeners = () => new Promise((resolve) => {
            const listItems = [...document.getElementsByClassName("js_pn-list-item")];
            listItems.forEach((item) => {
                item.addEventListener("click", selectCountry);
                item.addEventListener("keydown", function(e) {
                    const keyD = e.key !== undefined ? e.key : e.keyCode;
                    if (keyD === "Enter" || keyD === 13 || ["Spacebar", " "].indexOf(keyD) >= 0 || keyD === 32) {
                        e.preventDefault();
                        this.click();
                    }
                });
            });
            resolve();
        });

        const initiateList = () => {
            countryList = new List("js_pn-select", {
                valueNames: ["js_country-name", "js_country-prefix"],
            });

            // FIX 8: add/remove instead of toggle
            countryList.on("updated", (list) => {
                if (list.matchingItems.length < 5) {
                    listContainer.classList.add("pn-list--no-scroll");
                } else {
                    listContainer.classList.remove("pn-list--no-scroll");
                }
                noResultListItem.style.display =
                    list.matchingItems.length > 0 ? "none" : "block";
            });
        };

        await createList();
        await attachListItemEventListeners();
        initiateList();

        dropdownTrigger.addEventListener("click", () => {
            const isOpen = selectContainer.classList.contains("pn-select--open");
            isOpen ? closeDropdown() : openDropdown();
        });
    };

    const countries = [{
            name: "Austria",
            prefix: 43,
            flag: "at"
        },
        {
            name: "Belgium",
            prefix: 32,
            flag: "be"
        },
        {
            name: "Bulgaria",
            prefix: 359,
            flag: "bg"
        },
        {
            name: "Croatia",
            prefix: 385,
            flag: "hr"
        },
        {
            name: "Cyprus",
            prefix: 357,
            flag: "cy"
        },
        {
            name: "Czech Republic",
            prefix: 420,
            flag: "cz"
        },
        {
            name: "Denmark",
            prefix: 45,
            flag: "dk"
        },
        {
            name: "Estonia",
            prefix: 372,
            flag: "ee"
        },
        {
            name: "Finland",
            prefix: 358,
            flag: "fi"
        },
        {
            name: "France",
            prefix: 33,
            flag: "fr"
        },
        {
            name: "Germany",
            prefix: 49,
            flag: "de"
        },
        {
            name: "Greece",
            prefix: 30,
            flag: "gr"
        },
        {
            name: "Hungary",
            prefix: 36,
            flag: "hu"
        },
        {
            name: "Iceland",
            prefix: 354,
            flag: "is"
        },
        {
            name: "Republic of Ireland",
            prefix: 353,
            flag: "ie"
        },
        {
            name: "Italy",
            prefix: 39,
            flag: "it"
        },
        {
            name: "Latvia",
            prefix: 371,
            flag: "lv"
        },
        {
            name: "Liechtenstein",
            prefix: 423,
            flag: "li"
        },
        {
            name: "Lithuania",
            prefix: 370,
            flag: "lt"
        },
        {
            name: "Luxembourg",
            prefix: 352,
            flag: "lu"
        },
        {
            name: "Malta",
            prefix: 356,
            flag: "mt"
        },
        {
            name: "Netherlands",
            prefix: 31,
            flag: "nl"
        },
        {
            name: "Norway",
            prefix: 47,
            flag: "no"
        },
        {
            name: "Poland",
            prefix: 48,
            flag: "pl"
        },
        {
            name: "Portugal",
            prefix: 351,
            flag: "pt"
        },
        {
            name: "Romania",
            prefix: 40,
            flag: "ro"
        },
        {
            name: "Slovakia",
            prefix: 421,
            flag: "sk"
        },
        {
            name: "Slovenia",
            prefix: 386,
            flag: "si"
        },
        {
            name: "Spain",
            prefix: 34,
            flag: "es"
        },
        {
            name: "Sweden",
            prefix: 46,
            flag: "se"
        },
        {
            name: "Switzerland",
            prefix: 41,
            flag: "ch"
        },
        {
            name: "United Kingdom",
            prefix: 44,
            flag: "gb"
        },
        {
            name: "Ukraine",
            prefix: 380,
            flag: "ua"
        },
        {
            name: "Turkey",
            prefix: 90,
            flag: "tr"
        },
        {
            name: "Russia",
            prefix: 7,
            flag: "ru"
        },

        {
            name: "United States",
            prefix: 1,
            flag: "us"
        },
        {
            name: "Canada",
            prefix: 1,
            flag: "ca"
        },
        {
            name: "Mexico",
            prefix: 52,
            flag: "mx"
        },

        {
            name: "Morocco",
            prefix: 212,
            flag: "ma"
        },
        {
            name: "Saudi Arabia",
            prefix: 966,
            flag: "sa"
        },
        {
            name: "United Arab Emirates",
            prefix: 971,
            flag: "ae"
        },
        {
            name: "Qatar",
            prefix: 974,
            flag: "qa"
        },
        {
            name: "Kuwait",
            prefix: 965,
            flag: "kw"
        },
        {
            name: "Egypt",
            prefix: 20,
            flag: "eg"
        },
        {
            name: "Algeria",
            prefix: 213,
            flag: "dz"
        },
        {
            name: "Tunisia",
            prefix: 216,
            flag: "tn"
        },
        {
            name: "Jordan",
            prefix: 962,
            flag: "jo"
        },
        {
            name: "Lebanon",
            prefix: 961,
            flag: "lb"
        },
        {
            name: "Israel",
            prefix: 972,
            flag: "il"
        },

        {
            name: "China",
            prefix: 86,
            flag: "cn"
        },
        {
            name: "Japan",
            prefix: 81,
            flag: "jp"
        },
        {
            name: "South Korea",
            prefix: 82,
            flag: "kr"
        },
        {
            name: "India",
            prefix: 91,
            flag: "in"
        },
        {
            name: "Pakistan",
            prefix: 92,
            flag: "pk"
        },
        {
            name: "Bangladesh",
            prefix: 880,
            flag: "bd"
        },
        {
            name: "Indonesia",
            prefix: 62,
            flag: "id"
        },
        {
            name: "Malaysia",
            prefix: 60,
            flag: "my"
        },
        {
            name: "Singapore",
            prefix: 65,
            flag: "sg"
        },
        {
            name: "Thailand",
            prefix: 66,
            flag: "th"
        },
        {
            name: "Vietnam",
            prefix: 84,
            flag: "vn"
        },
        {
            name: "Philippines",
            prefix: 63,
            flag: "ph"
        },

        {
            name: "Nigeria",
            prefix: 234,
            flag: "ng"
        },
        {
            name: "South Africa",
            prefix: 27,
            flag: "za"
        },
        {
            name: "Kenya",
            prefix: 254,
            flag: "ke"
        },
        {
            name: "Ghana",
            prefix: 233,
            flag: "gh"
        },
        {
            name: "Ethiopia",
            prefix: 251,
            flag: "et"
        },
        {
            name: "Senegal",
            prefix: 221,
            flag: "sn"
        },

        {
            name: "Brazil",
            prefix: 55,
            flag: "br"
        },
        {
            name: "Argentina",
            prefix: 54,
            flag: "ar"
        },
        {
            name: "Colombia",
            prefix: 57,
            flag: "co"
        },
        {
            name: "Chile",
            prefix: 56,
            flag: "cl"
        },
        {
            name: "Peru",
            prefix: 51,
            flag: "pe"
        },

        {
            name: "Australia",
            prefix: 61,
            flag: "au"
        },
        {
            name: "New Zealand",
            prefix: 64,
            flag: "nz"
        },
    ];

    init(countries);
</script>