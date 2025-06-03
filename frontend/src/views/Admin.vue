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
        <div>
          <button v-if="beginLimit !== 0" @click="beginLimit = beginLimit - 20, endLimit = endLimit - 20">Vorige</button>
          <button v-if="endLimit < allData.data.length" @click="endLimit = endLimit + 20, beginLimit = beginLimit + 20">Volgende</button>
        </div>
        Items {{ beginLimit + 1 }} - {{ endLimit < allData.data.length ? endLimit : allData.data.length }} van {{ allData.data ? allData.data.length : 0 }}
      </div>
      <table>
        <thead>
          <tr>
        <th v-for="(header, index) in allData.data && allData.data.length ? Object.keys(allData.data[0]) : []" :key="index">
          <AdminTableHead :header="header" @headerClicked="headerClicked" :nameHeaderClicked="nameHeaderClicked" />
        </th>
          </tr>
        </thead>
        <tbody>
            <tr v-for="(row, rowIndex) in allData.data && allData.data.slice(beginLimit - 1, endLimit)" :key="rowIndex + beginLimit">
              <td v-for="(value, key, cellIndex) in row" :key="cellIndex">
                <span v-if="!isEditClicked[row.id]">{{ value }}</span>
                <input v-else-if="typeof value === 'string'" type="text" v-model="newData[key]" :placeholder="key" />
                <input v-else-if="typeof value === 'number'" type="number" v-model="newData[key]" :placeholder="key" />
              </td>
              <td>
                <span title="edit item" @click="editItem(row)"><i class="fa-solid fa-pen"></i></span>
              </td>
              <td v-if="Object.keys(allData.data[0]).includes('deleted') && !isEditClicked[row.id]">
                <span title="verwijder item" @click="itemAction('delete',table,row.id)"><i class="fa-solid fa-trash"></i></span>
              </td>
              <td v-if="Object.keys(allData.data[0]).includes('weergeven') && !isEditClicked[row.id]">
                <span v-if="row.weergeven === 1" title="verberg item" @click="itemAction('hide',table,row.id)"><i class="fa-solid fa-eye"></i></span>
                <span v-if="row.weergeven === 0" title="toon item" @click="itemAction('hide',table,row.id)"><i class="fa-solid fa-eye-slash"></i></span>
              </td>
              <td class="save" v-if="isEditClicked[row.id]">
                <span title="opslaan" @click="itemAction('edit',table,row.id)"><i class="fa-solid fa-check"></i></span>
              </td>
              <td class="save" v-if="isEditClicked[row.id]">
                <span title="annuleer" @click="isEditClicked[row.id] = false"><i class="fa-solid fa-xmark"></i></span>
              </td>
            </tr>
        </tbody>
      </table>
      <div v-if="allData.data" class="pagination">
        <div>
          <button v-if="beginLimit !== 0" @click="beginLimit = beginLimit - 20, endLimit = endLimit - 20">Vorige</button>
          <button v-if="endLimit < allData.data.length" @click="endLimit = endLimit + 20, beginLimit = beginLimit + 20">Volgende</button>
        </div>
        Items {{ beginLimit + 1 }} - {{ endLimit < allData.data.length ? endLimit : allData.data.length }} van {{ allData.data ? allData.data.length : 0 }}
      </div>
    </div>
  </main>

</template>
<script>
import { auth } from '@/auth';
import AdminTableHead from '@/components/AdminTableHead.vue';
  export default {
    name: 'Admin',
    components: {
      AdminTableHead
    },
    data() {
      return {
        // Add any data properties if needed
        allData:[],
        table: '',
        beginLimit: 1,
        endLimit: 20,
        nameHeaderClicked: '',
        newData: {},
        isEditClicked: {}
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
          this.table = this.allData.table || '';
        } catch (error) {
          console.error('There has been a problem with your fetch operation:', error);
        }
      },
      headerClicked(header, isClicked) {
        // Handle header click event
        console.log(`Header clicked: ${header}, isClicked: ${isClicked}`);
        this.nameHeaderClicked = header;
        // You can implement sorting or other functionality here
        if (!this.allData.data || !this.allData.data.length) return;

        const isNumber = this.allData.data.every(row => !isNaN(Number(row[header])) && row[header] !== null && row[header] !== '');

        this.allData.data.sort((a, b) => {
          let valA = a[header];
          let valB = b[header];

          if (isNumber) {
            valA = Number(valA);
            valB = Number(valB);
            return isClicked ? valA - valB : valB - valA;
          } else {
            valA = valA ? valA.toString().toLowerCase() : '';
            valB = valB ? valB.toString().toLowerCase() : '';
            if (valA < valB) return isClicked ? -1 : 1;
            if (valA > valB) return isClicked ? 1 : -1;
            return 0;
          }
        });
      },
      itemAction(action, table, id) {
        const formData = new FormData();
        if(action === 'delete'){
          if (!confirm('Weet je zeker dat je dit item wilt verwijderen?')) {
            return; // Stop the action if the user cancels
          }
        }
        formData.append('table', table);
        formData.append('id', id);
        formData.append('action', action);
        formData.append('data', JSON.stringify(this.newData));

        fetch(`${import.meta.env.VITE_APP_API_URL}backend/editItem`, {
          method: 'POST',
          body: formData,
          headers: {
            Authorization: auth.bearerToken
          }
        })
          .then(response => {
            if (response.ok) {
              this.fetchData(); // Refresh data after action
              if( action === 'edit') {
                this.isEditClicked[id] = false; // Exit edit mode after saving
                this.newData = {}; // Clear newData after saving
              }
            } else {
              console.error(`Error performing ${action} on item:`, response.statusText);
            }
          })
          .catch(error => {
            console.error(`Error performing ${action} on item:`, error);
          });
      },
      editItem(row) {
        // Handle edit item action
        this.isEditClicked[row.id] = !this.isEditClicked[row.id]; // Toggle edit mode for the row
        this.newData = { ...row }; // Copy the row data to newData for editing
        // You can implement a modal or form for editing here
        console.log('Editing item:', this.newData);
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
  gap: 1rem;
  border-radius: 0.25rem;

}
.databaseTablesItem > a {
  background-color: var(--color-card-500);
  border: 1px solid var(--color-secondary-500);
  padding: 10px;
  box-sizing: border-box;
  cursor: pointer;
  border-radius: 0.25rem;
  display: block;
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


th{
  cursor: pointer;
  background-color: var(--color-secondary-500);
}
td, th {
  border: 1px solid var(--color-primary-500);
  text-align: left;
  padding: 0.4rem 0.8rem;
  max-width: 20rem;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

tr:nth-child(even) {
  background-color: var(--color-card-500);
}

td i{
  cursor: pointer;
  color: var(--color-text);
  transition: 0.2s all;
}

td i:hover {
  color: var(--color-secondary-500);
}

td input[type="number"]{
  width: 5rem
}

.save{
  font-size: 100%;
  text-align: center;
}

.pagination {
  display: flex;
  justify-content: space-between;
  margin: 1rem 0;
}

.pagination div {
  display: flex;
  align-items: center;
  gap: 2rem;
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
