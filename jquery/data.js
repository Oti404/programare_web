/* ================================================
   DATA.JS — Date centralizate pentru aplicația Gestiune Parc Auto
   Folosit de: carousel.js, table.js, original.js, validation.js
   ================================================ */

/* ------- 1. Mărci și Modele (pentru dependențe câmpuri) ------- */
var MARCI_MODELE = {
    "Dacia":      ["Duster", "Logan", "Sandero", "Spring", "Jogger"],
    "Renault":    ["Clio", "Megane", "Kadjar", "Captur", "Arkana"],
    "Toyota":     ["Corolla", "Yaris", "RAV4", "C-HR", "Camry"],
    "Volkswagen": ["Golf", "Polo", "Passat", "Tiguan", "T-Roc"],
    "BMW":        ["Seria 1", "Seria 3", "Seria 5", "X3", "X5"],
    "Audi":       ["A3", "A4", "A6", "Q3", "Q5"],
    "Ford":       ["Fiesta", "Focus", "Kuga", "Mustang", "Puma"],
    "Skoda":      ["Fabia", "Octavia", "Superb", "Kodiaq", "Karoq"]
};

/* ------- 2. Județe și Localități (pentru dependențe câmpuri) ------- */
var JUDETE_LOCALITATI = {
    "Cluj":      ["Cluj-Napoca", "Dej", "Turda", "Câmpia Turzii", "Gherla"],
    "Mureș":     ["Târgu Mureș", "Reghin", "Sighișoara", "Târnăveni", "Luduș"],
    "Sibiu":     ["Sibiu", "Mediaș", "Cisnădie", "Avrig", "Copșa Mică"],
    "Brașov":    ["Brașov", "Făgăraș", "Săcele", "Codlea", "Zărnești"],
    "Prahova":   ["Ploiești", "Câmpina", "Sinaia", "Azuga", "Urlați"],
    "Ilfov":     ["Buftea", "Voluntari", "Pantelimon", "Popești-Leordeni"],
    "București": ["Sector 1", "Sector 2", "Sector 3", "Sector 4", "Sector 5", "Sector 6"],
    "Iași":      ["Iași", "Pașcani", "Hârlău", "Târgu Frumos"],
    "Timișoara": ["Timișoara", "Lugoj", "Sânnicolau Mare", "Recaș"]
};

/* ------- 3. Inventar Mașini (pentru tabel sortabil) ------- */
var MASINI_DATA = [
    { id: "AUTO-001", marca: "Dacia",      model: "Duster",   an: 2023, combustibil: "Benzină", transmisie: "Manuală",  km: 10000, pret: 15000, stare: "Nou" },
    { id: "AUTO-002", marca: "Renault",    model: "Clio",     an: 2022, combustibil: "Hibrid",  transmisie: "Automată", km: 35000, pret: 12500, stare: "Rulată" },
    { id: "AUTO-003", marca: "Toyota",     model: "Corolla",  an: 2021, combustibil: "Hibrid",  transmisie: "Automată", km: 45000, pret: 18000, stare: "Rulată" },
    { id: "AUTO-004", marca: "Volkswagen", model: "Golf",     an: 2023, combustibil: "Diesel",  transmisie: "Manuală",  km: 5000,  pret: 22000, stare: "Nou" },
    { id: "AUTO-005", marca: "BMW",        model: "Seria 3",  an: 2022, combustibil: "Benzină", transmisie: "Automată", km: 28000, pret: 35000, stare: "Rulată" },
    { id: "AUTO-006", marca: "Audi",       model: "A4",       an: 2021, combustibil: "Diesel",  transmisie: "Automată", km: 52000, pret: 29000, stare: "Rulată" },
    { id: "AUTO-007", marca: "Ford",       model: "Kuga",     an: 2023, combustibil: "Hibrid",  transmisie: "Automată", km: 8000,  pret: 28000, stare: "Nou" },
    { id: "AUTO-008", marca: "Skoda",      model: "Octavia",  an: 2022, combustibil: "Benzină", transmisie: "Manuală",  km: 41000, pret: 19500, stare: "Rulată" }
];

/* ------- 4. Carousel Slides ------- */
var CAROUSEL_SLIDES = [
    {
        link: "adaugare.html",
        text: "Dacia Duster 4x4 — SUV robust pentru orice teren",
        sub: "Disponibil de la 15.000 EUR · Benzină · Manuală",
        icon: "🚙",
        bg: "https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=1200&q=80",
        color: "#1a252f"
    },
    {
        link: "edit5.html",
        text: "Renault Clio Hybrid — Eficiență urbană maximă",
        sub: "De la 12.500 EUR · Hibrid · Automată",
        icon: "🏙️",
        bg: "https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=1200&q=80",
        color: "#16213e"
    },
    {
        link: "edit5.html",
        text: "Toyota Corolla Hybrid — Fiabilitate garantată",
        sub: "De la 18.000 EUR · Hibrid · Automată",
        icon: "⭐",
        bg: "https://images.unsplash.com/photo-1550355291-bbee04a92027?w=1200&q=80",
        color: "#0d2137"
    },
    {
        link: "widgets.html",
        text: "Dashboard Live — Monitorizare în timp real",
        sub: "124 vehicule livrate · Stoc: 45 mașini",
        icon: "📊",
        bg: "https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=1200&q=80",
        color: "#1c1c1c"
    },
    {
        link: "adaugare.html",
        text: "BMW Seria 3 — Performanță și eleganță germană",
        sub: "De la 35.000 EUR · Benzină · Automată",
        icon: "🏆",
        bg: "https://images.unsplash.com/photo-1555215695-3004980ad54e?w=1200&q=80",
        color: "#0a1628"
    }
];

/* ------- 5. Prețuri de bază pentru Calculator ------- */
var PRET_BAZA = {
    "Dacia":      { "Duster": 16000, "Logan": 12000, "Sandero": 11500, "Spring": 18000,  "Jogger": 17500 },
    "Renault":    { "Clio":   14000, "Megane": 18000, "Kadjar":  22000, "Captur": 20000, "Arkana": 23000 },
    "Toyota":     { "Corolla":22000, "Yaris":  16000, "RAV4":    35000, "C-HR":   28000, "Camry":  40000 },
    "Volkswagen": { "Golf":   24000, "Polo":   18000, "Passat":  32000, "Tiguan": 35000, "T-Roc":  28000 },
    "BMW":        { "Seria 1":35000, "Seria 3":45000, "Seria 5": 60000, "X3":     55000, "X5":     75000 },
    "Audi":       { "A3":     32000, "A4":     42000, "A6":      58000, "Q3":     38000, "Q5":     52000 },
    "Ford":       { "Fiesta": 14000, "Focus":  18000, "Kuga":    28000, "Mustang":50000, "Puma":   22000 },
    "Skoda":      { "Fabia":  13000, "Octavia":20000, "Superb":  32000, "Kodiaq": 36000, "Karoq":  28000 }
};

var DOTARI_PRET = {
    "calc_ac":              800,
    "calc_gps":             600,
    "calc_trapa":          1200,
    "calc_camera":          500,
    "calc_scaune":          700,
    "calc_pilot":          1500
};
