<template>
    <main>
        <section>
            <h1>Bedankt voor het nemen van een abonnement!</h1>
            <p>U ontvangt onmiddellijk een e-mail met de status van uw abonnement.</p>

            <a href="/" class="home-link">
                <i class="fa-solid fa-house" aria-hidden="true"></i>
                <span>Terug naar home</span>
            </a>
        </section>
    </main>
</template>

<script>
export default {
    name: 'Bedankt',
    data() {
        return {
            orderId: null, // Je kunt dit ophalen via query of localStorage
        };
    },
    created() {
        const params = new URLSearchParams(window.location.search);
        this.orderId = params.get('orderId');
    }
}
</script>

<style scoped>
main {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}

section {
    background: var(--color-card-500);
    border-radius: 1.5rem;
    padding: 2rem;
    box-shadow: 0.5rem 0.5rem 0.3rem 0.3rem var(--color-primary-500);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 3rem;
}

a {
    background: var(--color-primary-500);
    padding: 1rem 2rem;
    border-radius: 5rem;
    gap: 2rem;
    display: flex;
    flex-direction: row;
    width: max-content;
}

a p {
    display: inline;
}
</style>
