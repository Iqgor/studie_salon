<template>
  <main class="main">
    <div class="adminTop">
      <h1>Admin Dashboard</h1>
      <p>Welkom op het admin dashboard. Hier kun je gebruikers en andere gegevens beheren.</p>
      <div class="tables">
        <h2>Tabellen:</h2>
        <ul v-if="allData.tables" class="databaseTables">
          <li class="databaseTablesItem" v-for="(tables, index) in allData.tables" :key="index">
            <RouterLink :class="{ isClicked: tables === table }" :to="'?table=' + tables">{{ tables }}</RouterLink>
          </li>
        </ul>
      </div>
      <div v-if="allData.data" class="pagination">
        <div>
          <button v-if="beginLimit !== 0"
            @click="beginLimit = beginLimit - 20, endLimit = endLimit - 20">Vorige</button>
          <button v-if="endLimit < allData.data.length"
            @click="endLimit = endLimit + 20, beginLimit = beginLimit + 20">Volgende</button>
        </div>
        Items
        {{ filteredData.length > 0 ? '' : beginLimit + 1 }} -
        {{
          (filteredData.length > 0
            ? (endLimit < filteredData.length ? endLimit : filteredData.length) : (endLimit < allData.data.length ? endLimit
              : allData.data.length)) }} van {{ filteredData.length > 0
            ? filteredData.length
            : (allData.data ? allData.data.length : 0)
          }}
      </div>
      <div class="addItem">
        <h3>Item toevoegen:</h3>
        <button @click="editItem(null)">Toevoegen</button>
        <input @change="changeDB" @input="changeDB" type="text" v-model="zoekDB"
          :placeholder="`Zoek in de database tabel: ${table} `" />
      </div>
      <table>
        <thead>
          <tr>
            <th v-for="(header, index) in allData.data && allData.data.length ? Object.keys(allData.data[0]) : []"
              :key="index">
              <AdminTableHead :header="header" @headerClicked="headerClicked" :nameHeaderClicked="nameHeaderClicked" />
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(row, rowIndex) in filteredData.length > 0 ? filteredData.slice(beginLimit - 1, endLimit) : allData.data && allData.data.slice(beginLimit - 1, endLimit)"
            :key="rowIndex + beginLimit">
            <td v-for="(value, key, cellIndex) in row" :key="cellIndex">
              <span>{{ value }}</span>
            </td>
            <td>
              <span title="edit item" @click="editItem(row)"><i class="fa-solid fa-pen"></i></span>
            </td>
            <td>
              <span title="verwijder item" @click="itemAction('delete', table, row.id)"><i
                  class="fa-solid fa-trash"></i></span>
            </td>
            <td v-if="Object.keys(allData.data[0]).includes('weergeven') && !isEditClicked[row.id]">
              <span v-if="row.weergeven === 1" title="verberg item" @click="itemAction('hide', table, row.id)"><i
                  class="fa-solid fa-eye"></i></span>
              <span v-if="row.weergeven === 0" title="toon item" @click="itemAction('hide', table, row.id)"><i
                  class="fa-solid fa-eye-slash"></i></span>
            </td>
            <td class="save" v-if="isEditClicked[row.id]">
              <span title="opslaan" @click="itemAction('edit', table, row.id)"><i class="fa-solid fa-check"></i></span>
            </td>
            <td class="save" v-if="isEditClicked[row.id]">
              <span title="annuleer" @click="isEditClicked[row.id] = false"><i class="fa-solid fa-xmark"></i></span>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="allData.data" class="pagination">
        <div>
          <button v-if="beginLimit !== 0"
            @click="beginLimit = beginLimit - 20, endLimit = endLimit - 20">Vorige</button>
          <button v-if="endLimit < allData.data.length"
            @click="endLimit = endLimit + 20, beginLimit = beginLimit + 20">Volgende</button>
        </div>
        Items
        {{ filteredData.length > 0 ? '' : beginLimit + 1 }} -
        {{
          (filteredData.length > 0
            ? (endLimit < filteredData.length ? endLimit : filteredData.length) : (endLimit < allData.data.length ? endLimit
              : allData.data.length)) }} van {{ filteredData.length > 0
            ? filteredData.length
            : (allData.data ? allData.data.length : 0)
          }}
      </div>
    </div>
  </main>
  <div v-if="isEditClicked[newData.id] || isEditClicked['add']" class="editAddmodal">
    <div class="editAddmodalContent">
      <h2>Item Bewerken</h2>
      <div class="addIteminputs">
        <div :key="key" v-for="(data, key) in newData">
          <label :for="key">{{ key }}</label>
          <!--van bilal: omdat tinyint alleen 1 of 0 is. en de er zit int in tinyint dus deze eerst-->
          <input :id="key" v-if="allData.columnTypes[key].includes('tinyint')" type="number" min="0" max="1" step="1"
            v-model="newData[key]" :placeholder="key" />
          <input :id="key" v-else-if="allData.columnTypes[key].includes('decimal')" type="number" min="0"
            v-model="newData[key]" :placeholder="key" />
          <select :id="key" v-else-if="allData.columnTypes[key].startsWith('enum')" v-model="newData[key]">
            <option v-for="option in parseEnum(allData.columnTypes[key])" :key="option" :value="option">
              {{ option }}
            </option>
          </select>
          <input :id="key" v-else-if="allData.columnTypes[key].includes('date') && !allData.columnTypes[key].includes('datetime')" type="date" v-model="newData[key]" :placeholder="key" />



          <input :id="key" v-else-if="allData.columnTypes[key].includes('int')" type="number" v-model="newData[key]"
            :placeholder="key" />
          <input :id="key" v-else-if="allData.columnTypes[key].includes('char')" type="text" v-model="newData[key]"
            :placeholder="key" />
          <input :id="key" v-else-if="allData.columnTypes[key].includes('datetime')" type="datetime-local"
            v-model="newData[key]" :placeholder="key" />
          <textarea :id="key" v-else-if="allData.columnTypes[key] == 'text'" v-model="newData[key]"
            :placeholder="key" />
          <jodit-editor v-else-if="allData.columnTypes[key] == 'longtext'" v-model="newData[key]" :placeholder="key" />
        </div>
      </div>
      <div>
        <button
          @click="isEditClicked['add'] ? itemAction('add', table, newData.id) : itemAction('edit', table, newData.id)">Opslaan</button>
        <button @click="isEditClicked['add'] ? editItem(null) : editItem(newData)">Annuleren</button>
      </div>
    </div>
  </div>
</template>
<script>
import { auth } from '@/auth';
import AdminTableHead from '@/components/AdminTableHead.vue';
import 'jodit/build/jodit.min.css'
import { JoditEditor } from 'jodit-vue'
import { toastService } from '@/services/toastService';

export default {
  name: 'Admin',
  components: {
    AdminTableHead,
    JoditEditor
  },
  data() {
    return {
      // Add any data properties if needed
      allData: [],
      table: '',
      beginLimit: 1,
      endLimit: 20,
      nameHeaderClicked: '',
      newData: {},
      isEditClicked: {},
      zoekDB: '',
      filteredData: [],
    };
  },
  methods: {
    changeDB: (() => {
      // Debounce implementation to avoid lag on fast input changes
      let timeout = null;
      return function () {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
          if (this.zoekDB === '') {
            this.filteredData = []; // Reset filteredData to all data
            return;
          }
          this.filteredData = this.allData.data.filter(item => {
            return Object.values(item).some(value => {
              return value && value.toString().toLowerCase().includes(this.zoekDB.toLowerCase());
            });
          });
        }, 250); // 250ms debounce delay
      };
    })(),
    // Add any methods if needed
    async fetchData() {
      const formdata = new FormData();
      const urlParams = new URLSearchParams(window.location.search);
      const header = urlParams.get('table');
      this.table = header || '';
      formdata.append('table', this.table);
      try {
        const response = await fetch(`${import.meta.env.VITE_APP_API_URL}backend/adminInfo`, {
          method: 'POST',
          body: formdata,
          headers: {
            Authorization: auth.bearerToken
          }
        });
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        this.allData = await response.json();
        this.table = this.allData.table || '';
        this.changeDB(); // Call changeDB to filter data based on zoekDB input
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
      if (action === 'delete') {
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
            if (action === 'edit') {
              this.isEditClicked[id] = false; // Exit edit mode after saving
              this.newData = {}; // Clear newData after saving
            }
            if (action === 'add') {
              this.isEditClicked['add'] = false; // Exit add mode after saving
              this.newData = {}; // Clear newData after adding
            }
            document.body.style.overflow = '';
            toastService.addToast(`Item ${action}`, `Item is succsesvol ${action}`, 'success');
          } else {
            console.error(`Error performing ${action} on item:`, response.statusText);
          }
        })
        .catch(error => {
          console.error(`Error performing ${action} on item:`, error);
        });
    },
    editItem(row) {
      if (row === null) {
        // If row is null, it means we are adding a new item
        // Set newData with the keys from allData.data but with empty values
        this.newData = {};
        if (this.allData.data && this.allData.data.length) {
          Object.keys(this.allData.data[0]).forEach(key => {
            this.newData[key] = '';
          });
          // Set the id key to the last item's id
          const lastItem = this.allData.data[this.allData.data.length - 1];
          if (lastItem && lastItem.id !== undefined) {
            this.newData.id = lastItem.id + 1;
          }
        }
        this.isEditClicked['add'] = !this.isEditClicked['add']; // Set add mode
        document.body.style.overflow = 'hidden'; // Disable scrolling when modal is open
        return;
      }
      // Handle edit item action
      this.isEditClicked[row.id] = !this.isEditClicked[row.id]; // Toggle edit mode for the row
      this.newData = { ...row }; // Copy the row data to newData for editing
      document.body.style.overflow = this.isEditClicked[row.id] ? 'hidden' : '';
      // You can implement a modal or form for editing here
      console.log('Editing item:', this.newData);
    },
    parseEnum(typeStr) {
      // typeStr = "enum('%','$')"
      const match = typeStr.match(/^enum\((.+)\)$/);
      if (!match) return [];

      return match[1]
        .split(',')
        .map(value => value.trim().replace(/^'(.*)'$/, '$1')); // haalt 'quotes' weg
    }
  },
  watch: {
    // Watch for route changes to fetch data again
    '$route'() {
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
.tables {
  margin-top: 2rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;

}

.databaseTables {
  list-style-type: none;
  margin: 1rem 0;
  display: flex;
  width: 100%;
  flex-wrap: wrap;
  gap: 1rem;
  border-radius: 0.25rem;

}

.databaseTablesItem>a {
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

.addItem {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-top: 1rem;
}

.addItem input {
  padding: 0.5rem;
  border: 1px solid var(--color-secondary-500);
  border-radius: 0.25rem;
  width: 100%;
}

.addIteminputs {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
}

.addIteminputs input {
  flex: 1;
  min-width: 10rem;
  padding: 0.25rem;
  border: 1px solid var(--color-secondary-500);
  border-radius: 0.5rem;
  background: var(--color-background-500);
  color: var(--color-text);
}

.addIteminputs textarea {
  flex: 1;
  min-width: 10rem;
  padding: 0.25rem;
  border: 1px solid var(--color-secondary-500);
  border-radius: 0.5rem;
  background: var(--color-background-500);
  color: var(--color-text);
}

.addIteminputs select {
  padding: 0.5rem ;
  border-radius: 0.25rem;
  background-color: var(--color-background-500);
  color: var(--color-text);
}


.addItem button {
  padding: 0.5rem 1rem;
  background-color: var(--color-secondary-500);
  color: white;
  border: none;
  border-radius: 0.25rem;
  cursor: pointer;
  width: max-content;
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


th {
  cursor: pointer;
  background-color: var(--color-secondary-500);
}

td,
th {
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

td i {
  cursor: pointer;
  color: var(--color-text);
  transition: 0.2s all;
}

td i:hover {
  color: var(--color-secondary-500);
}

td input[type="number"] {
  width: 5rem
}

.save {
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

.editAddmodal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.editAddmodalContent {
  background-color: var(--color-card-500);
  padding: 2rem;
  border-radius: 0.25rem;
  width: 90%;
  max-width: 90rem;
  overflow-y: auto;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.editAddmodalContent h2 {
  margin-bottom: 1rem;
}

.editAddmodalContent .addIteminputs {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.editAddmodalContent input {
  padding: 0.5rem;
  border: 1px solid var(--color-secondary-500);
  border-radius: 0.25rem;
}

.editAddmodalContent button {
  padding: 0.5rem 1rem;
  background-color: var(--color-secondary-500);
  color: white;
  border: none;
  border-radius: 0.25rem;
  cursor: pointer;
  width: max-content;
}

.editAddmodalContent button:last-of-type {
  margin-left: 1rem;
}

.addIteminputs>div {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.addIteminputs label {
  font-weight: bold;
}
</style>
