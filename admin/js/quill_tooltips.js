// Credit: https://github.com/quilljs/quill/issues/650#issuecomment-541002924 modified to fit

let toolbarTooltips = {
    "font": "Schriftart auswählen",
    "size": "Schriftgröße auswählen",
    "header": "Absatzformat auswählen",
    "bold": "Fett (strg + b)",
    "italic": "Kursiv (strg + i)",
    "underline": "Unterstrichen (strg + u)",
    "strike": "Durchgestrichen",
    "color": "Textfarbe",
    "background": "Hintergrundfarbe",
    "script": {
        "sub": "Tiefgestellt",
        "super": "Hochgestellt"
    },
    "list": {
        "ordered": "nummerierte Liste",
        "bullet": "Liste"
    },
    "indent": {
        "-1": "Einrückung verkleinern",
        "+1": "Einrückung vergrößern"
    },
    "direction": {
        "rtl": "Text Richtung (rechts nach links | links nach rechts)",
        "ltr": "Text Richtung (links nach rechts | rechts nach links)"
    },
    "align": "Text Ausrichtung",
    "link": "Link einfügen (strg + k)",
    "image": "Bild einfügen",
    "formula": "Formel einfügen",
    "clean": "Formatierung löschen",
    "add-table": "Tabelle einfügen",
    "table-row": "Zeile zur ausgewählten Tabelle hinzufügen",
    "table-column": "Spalte zur ausgewählten Tabelle hinzufügen",
    "remove-table": "Ausgewählte Tabelle löschen",
    "help": "Hilfe",
    "clear": "Alles löschen",
    "blockquote": "Zitatblock"
};

function showTooltips() {
    let showTooltip = (which, el) => {
        if (which == "button") {
            var tool = el.className.replace("ql-", "");
        }
        else if (which == "span") {
            var tool = el.className.replace("ql-", "");
            tool = tool.substr(0, tool.indexOf(" "));
        }
        if (tool) {
            //if element has value attribute.. handling is different
            //buttons without value
            if (el.value == "") {
                if (toolbarTooltips[tool])
                    el.setAttribute("title", toolbarTooltips[tool]);
            }
            //buttons with value
            else if (typeof el.value !== "undefined") {
                if (toolbarTooltips[tool][el.value])
                    el.setAttribute("title", toolbarTooltips[tool][el.value]);
            }
            //default
            else
                el.setAttribute("title", toolbarTooltips[tool]);
        }
    };

    let toolbarElements = document.querySelectorAll(".ql-toolbar");
    if (toolbarElements) {
        for (const toolbarElement of toolbarElements) {
            let matchesButtons = toolbarElement.querySelectorAll("button");
            for (let el of matchesButtons) {
                showTooltip("button", el);
            }
            //for submenus inside
            let matchesSpans = toolbarElement.querySelectorAll(".ql-toolbar > span > span");
            for (let el of matchesSpans) {
                showTooltip("span", el);
            }
        }
    }
}
