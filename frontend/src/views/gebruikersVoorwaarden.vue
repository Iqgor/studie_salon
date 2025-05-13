<template>
    <section class="privacy-policy">
        <div class="container">
            <h1 class="title">STUDIE SALON / STUDY SALOON GEBRUIKERSVOORWAARDEN</h1>

            <p class="intro">
                Welkom bij Studie Salon | Study Saloon (SS/SS). Wij zijn de ontwikkelaars en exploitanten van een
                webapplicatie, later ook een smartphone app, waarin o.a. leren-leren strategieën, tips & tricks rondom
                zaken op school en daarbuiten, verhalen, sprookjes, quotes in zeven verschillende talen, en lifestyle
                tips worden aangeboden. Deze gebruikersvoorwaarden zijn van toepassing wanneer jij onze webapp – later
                ook de mobiele app – gebruikt.
            </p>

            <h2 class="section-title">ARTIKEL 1 - DEFINITIES</h2>
            <p class="text">
                In deze gebruikersvoorwaarden worden de volgende definities gehanteerd:
            </p>
            <ul class="list">
                <li><strong>SS/SS-abonnement:</strong> De overeenkomst waarmee je tegen betaling toegang krijgt tot de
                    Pro- of Premium-inhoud via de SS/SS-website.</li>
                <li><strong>SS/SS-webapp/-site:</strong> De webapp/-site van SS/SS, momenteel bekend als
                    www.studiesalon.nl.</li>
                <li><strong>SS/SS-account:</strong> Jouw persoonlijke login voor toegang tot de webapp/-site.</li>
                <li><strong>SS/SS-emailadres:</strong> <a class="link"
                        href="mailto:studiesalon.lerenlerentool@gmail.com">studiesalon.lerenlerentool@gmail.com</a></li>
                <li><strong>De overeenkomst:</strong> Zie artikel 2.</li>
            </ul>

            <h2 class="section-title">ARTIKEL 2 – OVEREENKOMST - ACCEPTATIE</h2>
            <p class="text">
                Gebruik van de webapp start zodra je de app installeert en akkoord gaat met deze voorwaarden via de
                knoppen "akkoord" en "18 jaar en ouder of ouder/voogd heeft beoordeeld". Zonder akkoord is gebruik niet
                toegestaan. Deze voorwaarden vormen een overeenkomst tussen Hollandse BreinBoosters (handelend onder
                Studie Salon) en jou als gebruiker.
            </p>

            <h2 class="section-title">ARTIKEL 3 – TOEPASSELIJKHEID</h2>
            <p class="text">
                Deze gebruikersvoorwaarden gelden vanaf het moment dat je de webapp gebruikt en/of een abonnement
                afsluit. Ga je niet akkoord, dan mag je de app niet gebruiken.
            </p>

            <h2 class="section-title">ARTIKEL 4 – INSTALLATIEVEREISTEN</h2>
            <p class="text">
                Je hebt eigen apparatuur met software nodig én internetverbinding. Je mag op minimaal twee apparaten
                inloggen. SS/SS is niet verantwoordelijk voor jouw hardware of verbinding.
            </p>

            <h2 class="section-title">ARTIKEL 5 – WIJZE VAN GEBRUIK</h2>
            <p class="text">
                De app is uitsluitend voor persoonlijk gebruik. Verstrek geen toegang aan derden. Je bent zelf
                verantwoordelijk voor veilige gegevens en correcte accountinformatie. Misbruik wordt bestraft.
            </p>

            <h2 class="section-title">ARTIKEL 6 – DE SS/SS-APP</h2>
            <p class="text">
                De app bevat software, content, animaties, en opslagen. SS/SS streeft naar 24/7 beschikbaarheid, maar
                kan geen garanties geven. Fouten worden zo snel mogelijk opgelost.
            </p>

            <h2 class="section-title">ARTIKEL 7 – KOSTEN & TARIEVEN</h2>
            <p class="text">
                De app biedt gratis en betaalde content. Prijzen zijn inclusief btw. Tarieven kunnen jaarlijks stijgen
                conform inflatie.
            </p>

            <h2 class="section-title">ARTIKEL 8 – BETALING & INCASSO</h2>
            <p class="text">
                Bij de start betaal je direct. Daarna maandelijks of jaarlijks via automatische incasso. Incasso wordt
                vooraf aangekondigd. Herinneringen en kosten volgen bij te late betaling.
            </p>

            <h2 class="section-title">ARTIKEL 9 – DUUR & BEËINDIGING</h2>
            <p class="text">
                Abonnementen verlengen stilzwijgend. Bij stopzetten of schorsing door SS/SS vindt geen restitutie
                plaats. Opzegging per e-mail met een maand opzegtermijn. Gegevens worden 5 jaar bewaard.
            </p>

            <h2 class="section-title">ARTIKEL 10 – INTELLECTUELE EIGENDOM</h2>
            <p class="text">
                SS/SS behoudt alle rechten. Zonder toestemming mag je niets kopiëren, wijzigen, verspreiden of
                commercieel gebruiken van de app.
            </p>

            <h2 class="section-title">ARTIKEL 11 – PRIVACY</h2>
            <p class="text">
                Jouw persoonsgegevens worden verwerkt volgens onze <a class="link"
                    href="/privacy">privacyverklaring</a>. Door akkoord te gaan met deze voorwaarden, stem je hier ook
                mee in.
            </p>

            <h2 class="section-title">ARTIKEL 12 – GEDRAGSCODE</h2>
            <p class="text">
                Gebruik de app volgens wet en fatsoen. Geen haat, virussen, hackpogingen of schendingen van rechten.
            </p>

            <h2 class="section-title">ARTIKEL 13 – GARANTIE DISCLAIMER</h2>
            <p class="text">
                Jij garandeert rechten van derden niet te schenden. SS/SS is niet aansprakelijk voor leerresultaten of
                beschikbaarheidsproblemen tijdens onderhoud.
            </p>

            <h2 class="section-title">ARTIKEL 14 – AANSPRAKELIJKHEID</h2>
            <p class="text">
                Behalve bij opzet of grove nalatigheid is de aansprakelijkheid van SS/SS beperkt tot het bedrag dat jij
                betaalde in de periode van de fout.
            </p>

            <p class="version">Versie: mei 2025</p>
        </div>
    </section>
</template>


<script>
export default {

    name: 'gebruikersVoorwaarden',
    setup() {
    },
    components: {
    },
    data() {
        return {
            contactEmail: 'studiesalon.lerenlerentool@gmail.com'
        };
    },
    mounted() {
        document.title = 'Privacyverklaring - Studie Salon';
    },
    methods: {
    }
}
</script>
<style scoped lang="css">
.privacy-policy {
    padding: 2rem;
    /* background-color: #f9f9f9; */
}

.container {
    max-width: 80rem;
    margin: 0 auto;
    padding: 2rem;
    background-color: var(--color-background-600);
    border-radius: 0.8rem;
    box-shadow: 0.5rem 0.5rem 0.4rem rgba(0, 0, 0, 0.1);
}

.title {
    font-size: 2rem;
    margin-bottom: 2rem;
    color: var(--color-text);
}

.intro {
    font-size: 1.5rem;
    margin-bottom: 2rem;
    color: var(--color-text);
}

.text {
    font-size: 1.5rem;
    margin-bottom: 2rem;
    color: var(--color-text);
}

.section-title {
    font-size: 1.5rem;
    margin-top: 3rem;
    margin-bottom: 1rem;
    color: var(--color-text);
}

.list {
    list-style-type: disc;
    margin-left: 2rem;
    margin-bottom: 2rem;
}

.link {
    color: var(--color-primary-500);
    text-decoration: none;
}

.link:hover {
    text-decoration: underline;
}

.version {
    font-size: 0.9rem;
    color: var(--color-text);
    margin-top: 2rem;
}

@media (max-width: 600px) {
    .container {
        padding: 1rem;
    }

    .title {
        font-size: 1.5rem
    }

    .section-title {
        font-size: 1.3rem;
    }
}
</style>