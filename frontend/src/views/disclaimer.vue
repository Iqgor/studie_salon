<template>
    <section class="section">
        <div class="container">
<h1 class="title">Disclaimer voor SS/SS</h1>
            <article class="card">
                <p class="text">
                    De informatie en tips die op SS/SS worden gedeeld, zijn onder andere bedoeld om je te ondersteunen bij het ontwikkelen van effectieve studievaardigheden, studiestrategieën en andere persoonsgerichte of schoolgerelateerde zaken. SS/SS kan echter niet aansprakelijk worden gesteld voor de wijze waarop deze adviezen worden toegepast of uitgevoerd. Het is de verantwoordelijkheid van de gebruiker om te bepalen of de voorgestelde studieaanpak geschikt is voor zijn of haar persoonlijke situatie en voor de feitelijke toepassing van de studievaardigheden.
                </p>
                

                <p class="text">
                    Alle informatie, strategieën en technieken die worden gepresenteerd, dienen als algemene adviezen en kunnen variëren in effectiviteit, afhankelijk van individuele voorkeuren en omstandigheden. Wij kunnen niet verantwoordelijk worden gehouden voor de uitkomsten van het gebruik van de voorgestelde methoden. De verantwoordelijkheid voor de toepassing en de resultaten hiervan ligt bij de gebruiker zelf.
                </p>
                

                <p class="text">
                    De gebruiker maakt gebruik van SS/SS en de aangeboden opties op eigen risico. Wij raden ouders aan – omdat het gaat om hun minderjarige kind(eren) - om betrokken te blijven bij het studieproces van hun kinderen en hen te begeleiden in de keuze en toepassing van de adviezen die hier worden gegeven.
                </p>
                



                <p class="text">
                    Door gebruik te maken van deze webapp, accepteer je de voorwaarden in de disclaimer en erken je dat jijzelf (of de ouders/verzorgers van minderjarige gebruikers) verantwoordelijk bent voor het gebruik van de geboden informatie en strategieën.
                </p>

                <p class="text">
                    Als iets niet goed voelt, terwijl het wel in de webapp wordt aangeboden, gebruik het dan niet.
                </p>
                <p class="text">
                    Omdat de webapp nog niet helemaal af is, gebruiken we een speciaal tarief.
                </p>
            </article>
            


            

           
        </div>
    </section>


</template>
<script>
export default {

    name: 'disclaimer',
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
        document.title = 'privacyVerklaring - Studie Salon';
    },
    methods: {
    }
}
</script>
<style scoped lang="css">

.card {
    padding: 2rem;
    margin-bottom: 2rem;
    background-color: var(--color-background-700);
    border-radius: 0.8rem;
    box-shadow: 0.5rem 0.5rem 0.4rem rgba(0, 0, 0, 0.3);
}

.section {
    padding: 2rem;
}

.container {
    max-width: 80rem;
    margin: 0 auto;
    padding: 2rem;
    border-radius: 0.8rem;
}

.title {
    font-size: 2.5rem;
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
    font-size: 2rem;
    margin-bottom: 1rem;
    color: var(--color-text);
}

.list {
    list-style-type: disc;
    margin-left: 2rem;
    margin-bottom: 2rem;
}

.list li {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    color: var(--color-text);
}
.list li::marker {
    color: var(--color-primary-500);
}

.link {
    color: var(--color-primary-500);
    text-decoration: none;
    text-decoration: underline;
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