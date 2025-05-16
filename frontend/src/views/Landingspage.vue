<template>
  <main class="landingspage-top">
    <section class="sectie-index">
      <div class="sectie-links">
        <h2 class="landingspage-h2">Leer actief leren,</h2>
        <p class="landingspage-ondertitel">Leren <b class="sectie-links-kleur">actiever</b> en <b class="sectie-links-kleur">leuker</b> gemaakt door de <b class="sectie-links-kleur">LerenLerenTool</b>.</p>
        <p class="landingspage-ondertitel">Bekijk onze verschillende abonnement <b class="sectie-links-kleur"><router-link :to="'/plans'">hier</router-link></b></p>
      </div>
      <img src="../assets/iphone-app-hand.webp" alt="" class="sectie-midden-img" />
    </section>
    <!-- vakjes gedeelte -->
    <section class="landingspage-sectie">
      <div class="container-landingspage-box">
        <div class="landingspage-box">
          <div class="icon">✔️</div>
          <h2>Waarom ons?</h2>
          <p>Wij bieden praktische leerstrategieën die werken: meer resultaat in minder tijd. Persoonlijk, efficiënt en gericht op meer zelfvertrouwen. Zo wordt leren makkelijker en succesvoller.</p>
        </div>
        <div class="landingspage-box">
          <div class="icon">💡</div>
          <h2>Onze methode</h2>
          <p>Leer actief en effectief met onze methoden: besteed 20% van de tijd om 80% van het resultaat te behalen. Geen tijdverspilling, maar efficiënt leren voor betere resultaten, minder stress en meer zelfvertrouwen.</p>
        </div>
        <div class="landingspage-box">
          <div class="icon">⚡</div>
          <h2>Snel en eenvoudig</h2>
          <p>Leren kan altijd en overal. Met onze flexibele methoden leer je efficiënt, zonder lang stil te zitten. Samenwerken met anderen maakt leren sneller, effectiever en leuker!</p>
        </div>
        <div class="landingspage-box purple">
          <div class="icon">🌐</div>
          <h2>Meerdere talen</h2>
          <p>Onze effectieve studiestrategieën zijn beschikbaar op twee niveaus en in 14 talen, waaronder Nederlands, Engels, Frans, Spaans, Mandarijn en Arabisch. Teksten kunnen ook worden voorgelezen voor extra toegankelijkheid.</p>
        </div>
      </div>
    </section>
  </main>
</Template>
<script>

</script>
<style>

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
  padding: 30px;
  border-radius: 8px;
}

.landingspage-box .icon {
  font-size: 5rem;
  margin-bottom: 15px;
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
