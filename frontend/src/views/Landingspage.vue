<template>
  <main class="landingspage-top">
    <section class="sectie-index">
      <div class="sectie-links">
        <h2 class="landingspage-h2">Leer actief leren,</h2>
        <p class="landingspage-ondertitel">Met onze <b class="sectie-links-kleur">LerenLerenTool</b> wordt leren <b class="sectie-links-kleur">leuker</b> en <b class="sectie-links-kleur">makkelijker</b>.</p>
        <p class="landingspage-ondertitel">Bekijk onze verschillende abonnement <b class="sectie-links-kleur"><router-link :to="'/abonnementen'">hier</router-link></b>.</p>
      </div>
      <img src="../assets/iphone-app-hand.webp" alt="" class="sectie-midden-img" />
    </section>
    <!-- vakjes gedeelte -->
    <section class="landingspage-sectie">
      <div class="container-landingspage-box">
        <div class="landingspage-box">
          <div class="icon">✅</div>
          <h2>Waarom kiezen voor ons?</h2>
          <p>We bieden makkelijke en snel te gebruiken LerenLerenStragegieën, gericht op de leerstijl die het beste bij jou past. Welke leerstijl heb beste bij jou past, ontdek je snel door een eenvoudige quiz. Leuk weetje: voor elk vak kan je je eigen leerstijl ontdekken! </p>
        </div>
        <div class="landingspage-box">
          <div class="icon">💡</div>
          <h2>Onze methode</h2>
          <p>Met onze tips & tricks kun je in 20% van de tijd voor 80% van je cijfer leren. Geen tijdsverspilling met 'hoe begin ik', maar meteen aan de slag met leuke en makkelijke LerenLerenStrategieën die werken. Zo haal je in minder tijd een beter cijfer!</p>
        </div>
        <div class="landingspage-box">
          <div class="icon">⚡</div>
          <h2>Snel en eenvoudig</h2>
          <p>Leren kan altijd en overal. Door de talloze flexibele tips kan je overal en altijd éven leren, met of zonder AI, zonder dat je lang stil zit. Zo kun je ook tussen het sporten door of als je ergens moet wachten, je leerwerk erbij pakken. Dat scheelt thuis weer!</p>
        </div>
        <div class="landingspage-box purple">
          <div class="icon">🌐</div>
          <h2>Meerdere niveaus, talen, voorgelezen en ondersteund door muziek</h2>
          <p>Al onze tips & tricks zijn beschikbaar op drie niveaus en in alle denkbare talen. Je kunt de teksten laten voorlezen door een stem die je aanspreekt en ze kunnen worden ondersteund door muziek voor extra focus, als dat voor jou werkt. Door de online agenda met planhulp, kun je snel je dag plannen, organiseren en ook prioriteiten stellen.</p>
        </div>
      </div>
    </section>
  </main>
</Template>
<script>

</script>
<style scoped>

.sectie-index {
  display: flex;
  justify-content: space-between;
  padding: 5rem 10rem 0rem 10rem;
  height:86vh;
}
.sectie-links {
  width: 35%;
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.landingspage-h2 {
  font-size: 6rem;
  color: var(--color-text-500);
  font-weight: 500;

}

.landingspage-ondertitel {
  font-size: 3rem;
  color: var(--color-text-500);
  font-weight: 500;

}
.sectie-links-kleur {
  color: var(--color-primary-500);
  font-weight: 500;
}

.sectie-links-kleur > a{
  color: var(--color-primary-500);
}

.sectie-links-kleur > a:hover{
  color: var(--color-primary-300);
}

.sectie-rechts {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.sectie-midden-img {
  width: 35%;
  object-fit: contain;
}

/* onderste sectie */

.container-landingspage-box {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-around;
  padding-bottom: 2rem;
  gap: 2rem;
}

.landingspage-box {
  max-width: 35rem;
  padding: 2rem;
  border-radius: 8px;
}

.landingspage-box .icon {
  font-size: 5rem;
  margin-bottom: 1.5rem;
}

.landingspage-box h2 {
  margin-bottom: 10px;
  font-size: 24px;
}

.landingspage-box p {
  font-size: 16px;
  line-height: 1.5;
}

.landingspage-box:nth-child(2) {
  background-color: var(--color-primary-500);
  color: var(--color-white);
}

.landingspage-box:nth-child(4) {
  background-color: var(--color-secondary-500);
  color: var(--color-white);

}

.landingspage-box h2, .landingspage-box p {
  color: var(--color-text-500);
}
</style>
