<template>
    <RouterLink v-if="!isEditClicked" :to="`${slug}/${item.slug}`" v-html="item.name "></RouterLink>
    <input v-else type="text" v-model="item.name" class="editLink" />
    <i v-if="isAdmin" @click="isEditClicked = !isEditClicked" class="fa-solid fa-pen"></i>
    <i v-if="isEditClicked" @click="editLink" class="fa-regular fa-circle-check"></i>
</template>
<script>
import { toastService } from '@/services/toastService';
import { auth } from '@/auth';
export default {
  name: "VoorAfLinks",
  props: {
    item: {
      type: Object,
      required: true
    },
    slug: {
      type: String,
      required: true
    },
    isAdmin: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      isEditClicked: false,
    };
  },
  methods: {
    editLink() {
      const formData = new FormData();
      formData.append('slug', this.item.slug);
      formData.append('name', this.item.name);
      fetch(`${import.meta.env.VITE_APP_API_URL}backend/editLink`, {
        method: 'POST',
        body: formData,
        headers: {
          Authorization: auth.bearerToken
        }
      })
        .then(response => response.json())
        .then(() => {
          toastService.addToast('Link aangepast', 'De link is aangepast', 'success');
          this.isEditClicked = false;
        })
        .catch(error => {
          console.error('Error sending text', error);
        });

    }
  }
};

</script>
<style >

.view  .fa-circle-check{
  color: var(--color-primary-400);
  cursor: pointer;
  transition: 0.25s all;
}

.view  .fa-circle-check:hover{
  color: rgb(86, 234, 86);
}

.view >li > i{
  color: var(--color-primary-400);
  transition: all 0.3s ease;
  cursor: pointer;
  font-size: 80%;
}
.view >li > i:hover{
  color: var(--color-primary-700);
}


.tableView>li>a {
  display: block;
  width: 100%;
  text-align: center;
}

.tableView>li:hover {
  background-color: var(--color-card-600);
}


.tableView a:hover{
  color: var(--color-text);

}

</style>
