<template>
  <main class="main">
    <div class="adminTop">
      <h1>Admin Dashboard</h1>
      <p>Welkom op het admin dashboard. Hier kun je gebruikers en andere gegevens beheren.</p>
      <div class="tables">
        <h2>Tabellen:</h2>
        <ul v-if="allData.tables" class="databaseTables">
          <li class="databaseTablesItem" v-for="(tables,index ) in allData.tables" :key="index" ><RouterLink :class="{ isClicked: tables === table }" :to="'?table=' + tables">{{tables}}</RouterLink></li>
        </ul>
      </div>
      <div v-if="allData.data" class="pagination">
        <button v-if="beginLimit !== 0" @click="beginLimit = beginLimit - 20, endLimit = endLimit - 20">Vorige</button>
        <button v-if="endLimit < allData.data.length" @click="endLimit = endLimit + 20, beginLimit = beginLimit + 20">Volgende</button>
        Items {{ beginLimit + 1 }} - {{ endLimit < allData.data.length ? endLimit : allData.data.length }} van {{ allData.data ? allData.data.length : 0 }}

      </div>
      <table>
        <thead>
          <tr>
        <th v-for="(header, index) in allData.data && allData.data.length ? Object.keys(allData.data[0]) : []" :key="index">
          {{ header }}
        </th>
          </tr>
        </thead>
        <tbody>
            <tr v-for="(row, rowIndex) in allData.data && allData.data.slice(beginLimit - 1, endLimit)" :key="rowIndex + beginLimit">
            <td v-for="(value, key, cellIndex) in row" :key="cellIndex">
              {{ value }}
            </td>
            </tr>
        </tbody>
      </table>
      <div v-if="allData.data" class="pagination">
        <button v-if="beginLimit !== 0" @click="beginLimit = beginLimit - 20, endLimit = endLimit - 20">Vorige</button>
        <button v-if="endLimit < allData.data.length" @click="endLimit = endLimit + 20, beginLimit = beginLimit + 20">Volgende</button>
        Items {{ beginLimit + 1 }} - {{ endLimit < allData.data.length ? endLimit : allData.data.length }} van {{ allData.data ? allData.data.length : 0 }}
      </div>
    </div>
  </main>

</template>
<script>
import { auth } from '@/auth';
  export default {
    name: 'Admin',
    data() {
      return {
        // Add any data properties if needed
        allData:[],
        table: '',
        beginLimit: 1,
        endLimit: 20
      };
    },
    methods: {
      // Add any methods if needed
      async fetchData() {
        const formdata = new FormData();
        const urlParams = new URLSearchParams(window.location.search);
        const header = urlParams.get('table');
        this.table = header || '';
        formdata.append('table', this.table);
        try {
          const response = await fetch(`${import.meta.env.VITE_APP_API_URL}backend/adminInfo`,{
            method:'POST',
            body: formdata,
            headers: {
              Authorization: auth.bearerToken
            }
          });
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          this.allData = await response.json();
          this.allData = this.allData // Assuming the data is in a 'data' property
        } catch (error) {
          console.error('There has been a problem with your fetch operation:', error);
        }
      }
    },
    watch: {
      // Watch for route changes to fetch data again
      '$route'(to, from) {
      this.fetchData();
      this.beginLimit = 1; // Reset pagination on route change
      this.endLimit = 20; // Reset pagination on route change
      }
    },
    mounted() {
      // Code to run when the component is mounted
      this.fetchData();
    }
  };
</script>
<style scoped>
.tables{
  margin-top: 2rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;

}
.databaseTables {
  list-style-type: none;
  margin:1rem 0;
  display: flex;
  width: 100%;
  flex-wrap: wrap;
  justify-content: space-between;
  border-radius: 0.25rem;

}
.databaseTablesItem > a {
  background-color: var(--color-card-500);
  border: 1px solid var(--color-secondary-500);
  padding: 10px;
  box-sizing: border-box;
  cursor: pointer;
  border-radius: 0.25rem;
}

.isClicked {
  background-color: var(--color-secondary-500) !important;
  color: black;
}

table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
  margin-top: 1rem;
  display: block;
  overflow-x: auto;
  max-width: 100vw;
  white-space: nowrap;
  border-radius: 0.25rem;
}

td, th {
  border: 1px solid var(--color-primary-500);
  text-align: left;
  padding: 4px 8px;
  min-width: 80px;
  max-width: 200px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

tr:nth-child(even) {
  background-color: var(--color-card-500);
}

.pagination {
  display: flex;
  justify-content: space-between;
  margin: 1rem 0;
}

.pagination button {
  padding: 0.5rem 1rem;
  background-color: var(--color-card-500);
  color: var(--color-text);
  border: none;
  border-radius: 0.25rem;
  cursor: pointer;
}
</style>
