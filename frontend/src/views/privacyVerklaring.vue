<template>
   <div v-html="tekst"></div>


</template>
<script>
import { auth } from '@/auth';
export default {

    name: 'PrivacyVerklaring',
    setup() {
    },
    components: {
    },
    data() {
        return {
            contactEmail: 'studiesalon.lerenlerentool@gmail.com',
            slug: 'privacy-verklaring',
            tekst: '',
            succes: false,
            loading: true,
            editedContent: '',
            isEditClicked: false,
        };
    },
    mounted() {
        document.title = 'privacyVerklaring - Studie Salon';
        this.getTekst();
    },
    methods: {
    async getTekst() {
      this.loading = true;
      const formData = new FormData();
      formData.append('slug', this.slug.replace('/', '-'));
      await fetch(`${import.meta.env.VITE_APP_API_URL}backend/getTekst`, {
        method: 'POST',
        body: formData,
        headers: {
          Authorization: auth.bearerToken
        }
      })
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(data => {
          auth.checkAction(data?.action)
          if (data.tekst.length !== 0) {
            this.succes = true;
            this.loading = false;
            this.tekst = data.tekst;
            this.editedContent = data.tekst;
          } else {
            this.succes = false;
            this.loading = false;
          }
        })
        .catch(error => {
          this.loading = false;

          console.error('Er is een fout opgetreden:', error);
        });
    }
    }
}
</script>
<style >

.cardPri {
    padding: 2rem;
    margin-bottom: 2rem;
    background-color: var(--color-card-500);
    border-radius: 0.8rem;
    box-shadow: 0.5rem 0.5rem 0.4rem rgba(0, 0, 0, 0.3);
}

.sectionPri {
    padding: 2rem;
}

.containerPri {
    max-width: 80rem;
    margin: 0 auto;
    padding: 2rem;
    border-radius: 0.8rem;
}

.titlePri {
    font-size: 2.5rem;
    margin-bottom: 2rem;
    color: var(--color-text);
}

.introPri {
    font-size: 1.5rem;
    margin-bottom: 2rem;
    color: var(--color-text);
}

.textPri {
    font-size: 1.5rem;
    margin-bottom: 2rem;
    color: var(--color-text);
}

.section-titlePri {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: var(--color-text);
}

.listPri {
    list-style-type: disc;
    margin-left: 2rem;
    margin-bottom: 2rem;
}

.listPri li {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    color: var(--color-text);
}
.listPri li::marker {
    color: var(--color-primary-500);
}

.linkPri {
    color: var(--color-primary-500);
    text-decoration: none;
    text-decoration: underline;
}

.linkPri:hover {
    text-decoration: underline;
}

.versionPri {
    font-size: 0.9rem;
    color: var(--color-text);
    margin-top: 2rem;
}

@media (max-width: 600px) {
    .containerPri {
        padding: 1rem;
    }

    .titlePri {
        font-size: 1.5rem
    }

    .section-titlePri {
        font-size: 1.3rem;
    }
}
</style>
