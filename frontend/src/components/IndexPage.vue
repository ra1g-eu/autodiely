<template>
  <v-main>
    <v-container fluid>
      <v-row>
        <v-col cols="12">
          <h1 class="text-h4 mb-4">Car Inventory</h1>
          <v-text-field
            v-model="carSearchQuery"
            label="Search Cars"
            prepend-inner-icon="mdi-magnify"
            single-line
            hide-details
            class="mb-4"
          ></v-text-field>
          <v-btn color="primary" @click="openCarModal(null)">Add Car</v-btn>
          <v-table class="mt-4">
            <thead>
            <tr>
              <th>
                <span @click="sortCars('name')">Name</span>
                <v-icon small>{{ sortIcon('name') }}</v-icon>
              </th>
              <th>
                <span @click="sortCars('registration_number')">Registration Number</span>
                <v-icon small>{{ sortIcon('registration_number') }}</v-icon>
              </th>
              <th>
                <span @click="sortCars('is_registered')">Is Registered</span>
                <v-icon small>{{ sortIcon('is_registered') }}</v-icon>
              </th>
              <th>
                <span @click="sortCars('updated_at')">Last Update</span>
                <v-icon small>{{ sortIcon('updated_at') }}</v-icon>
              </th>
              <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="car in filteredAndSortedCars" :key="car.id">
              <td>{{ car.name }}</td>
              <td>{{ car.registration_number || 'N/A' }}</td>
              <td>{{ car.is_registered ? 'Yes' : 'No' }}</td>
              <td>{{ new Date(car.updated_at).toLocaleString() }}</td>
              <td>
                <v-btn icon size="small" @click="openCarModal(car)">
                  <v-icon>mdi-pencil</v-icon>
                  <v-tooltip
                    activator="parent"
                    location="top"
                  >Update car
                  </v-tooltip>
                </v-btn>
                <v-btn icon size="small" @click="showParts(car)">
                  <v-icon>mdi-eye</v-icon>
                  <v-tooltip
                    activator="parent"
                    location="top"
                  >Show Parts
                  </v-tooltip>
                </v-btn>
                <v-btn icon size="small" color="error" @click="openDeleteDialog('car', car)">
                  <v-icon>mdi-delete</v-icon>
                  <v-tooltip
                    activator="parent"
                    location="top"
                  >Delete car
                  </v-tooltip>
                </v-btn>
              </td>
            </tr>
            </tbody>
          </v-table>
        </v-col>
      </v-row>

      <v-row v-if="selectedCar">
        <v-col cols="12">
          <h2 class="text-h5 mt-8">Parts for Car: {{ selectedCar.name }}</h2>
          <v-text-field
            v-model="partSearchQuery"
            label="Search Parts"
            prepend-inner-icon="mdi-magnify"
            single-line
            hide-details
            class="mb-4"
          ></v-text-field>
          <v-btn color="primary" @click="openPartModal(null)">Add Part</v-btn>
          <v-table class="mt-4">
            <thead>
            <tr>
              <th>
                <span @click="sortParts('name')">Name</span>
                <v-icon small>{{ sortIcon('name', 'parts') }}</v-icon>
              </th>
              <th>
                <span @click="sortParts('serialnumber')">Serial Number</span>
                <v-icon small>{{ sortIcon('serialnumber', 'parts') }}</v-icon>
              </th>
              <th>Last Update</th>
              <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="part in filteredAndSortedParts" :key="part.id">
              <td>{{ part.name }}</td>
              <td>{{ part.serialnumber }}</td>
              <td>{{ new Date(part.updated_at).toLocaleString() }}</td>
              <td>
                <v-btn icon size="small" @click="openPartModal(part)">
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
                <v-btn icon size="small" color="error" @click="openDeleteDialog('part', part)">
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </td>
            </tr>
            </tbody>
          </v-table>
        </v-col>
      </v-row>
    </v-container>

    <!-- Car Modal -->
    <v-dialog v-model="isCarModalOpen" max-width="600px">
      <v-card>
        <v-card-title>
          <span class="text-h5">{{ editCar ? 'Edit Car' : 'Add Car' }}</span>
        </v-card-title>
        <v-card-text>
          <v-form ref="carForm">
            <v-text-field label="Name" v-model="formCar.name" :rules="[v => !!v || 'Name is required']"></v-text-field>
            <v-checkbox label="Is Registered?" v-model="formCar.is_registered"></v-checkbox>
            <v-text-field v-if="formCar.is_registered" label="Registration Number" v-model="formCar.registration_number"
                          :rules="[v => !!v || 'Registration Number is required']"></v-text-field>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue-darken-1" variant="text" @click="closeCarModal">Cancel</v-btn>
          <v-btn color="blue-darken-1" variant="text" @click="saveCar">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Part Modal -->
    <v-dialog v-model="isPartModalOpen" max-width="600px">
      <v-card>
        <v-card-title>
          <span class="text-h5">{{ editPart ? 'Edit Part' : 'Add Part' }}</span>
        </v-card-title>
        <v-card-text>
          <v-form ref="partForm">
            <v-text-field label="Name" v-model="formPart.name" :rules="[v => !!v || 'Name is required']"></v-text-field>
            <v-text-field label="Serial Number" v-model="formPart.serialnumber"
                          :rules="[v => !!v || 'Serial Number is required']"></v-text-field>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue-darken-1" variant="text" @click="closePartModal">Cancel</v-btn>
          <v-btn color="blue-darken-1" variant="text" @click="savePart">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="isDeleteDialogOpen" max-width="400px">
      <v-card>
        <v-card-title class="text-h6">Confirm Delete</v-card-title>
        <v-card-text>Are you sure you want to delete this {{ deleteType }}?</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue-darken-1" variant="text" @click="closeDeleteDialog">Cancel</v-btn>
          <v-btn color="red-darken-1" variant="text" @click="confirmDelete">Delete</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-snackbar v-model="showSnackbar" :color="snackbarColor" :timeout="3000">
      {{ snackbarMessage }}
      <template v-slot:actions>
        <v-btn color="white" variant="text" @click="showSnackbar = false">Close</v-btn>
      </template>
    </v-snackbar>
  </v-main>
</template>

<script>
import axios from "axios";

export default {
  name: 'IndexPage',
  data() {
    return {
      cars: [],
      selectedCar: null,
      isCarModalOpen: false,
      isPartModalOpen: false,
      isDeleteDialogOpen: false,
      formCar: {name: '', registration_number: '', is_registered: false},
      formPart: {name: '', serialnumber: '', car_id: null},
      editCar: false,
      editPart: false,
      deleteType: '', // 'car' or 'part'
      itemToDelete: null,
      showSnackbar: false,
      snackbarMessage: '',
      snackbarColor: 'success',
      carSearchQuery: '',
      partSearchQuery: '',
      carSortKey: '',
      carSortOrder: 1,
      partSortKey: '',
      partSortOrder: 1, // 1 for ascending, -1 for descending
    };
  },
  methods: {
    async fetchCars() {
      const response = await axios.get('http://127.0.0.1:8000/api/cars');
      if (response.data.success) {
        this.cars = response.data.message.map(car => ({...car, parts: []}));
        this.sortCars('updated_at');
      }
    },
    async fetchPartsForCar(carId) {
      const response = await axios.get(`http://127.0.0.1:8000/api/parts/car/${carId}`);
      this.selectedCar.parts = response.data.message;
    },
    openCarModal(car) {
      if (car) {
        this.editCar = true;
        this.formCar = {...car};
      } else {
        this.editCar = false;
        this.formCar = {name: '', registration_number: '', is_registered: false};
      }
      this.isCarModalOpen = true;
    },
    closeCarModal() {
      this.isCarModalOpen = false;
      this.$refs.carForm.reset();
    },
    async saveCar() {
      if (!this.$refs.carForm.validate()) return;
      let response = null;

      try {
        if (this.editCar) {
          response = await axios.put(`http://127.0.0.1:8000/api/cars/${this.formCar.id}`, this.formCar);
        } else {
          response = await axios.post('http://127.0.0.1:8000/api/cars', this.formCar);
        }
        if (response && response.data.success) {
          this.showSnackbarMessage('Operation successful', 'success');
          if (this.editCar) {
            const index = this.cars.findIndex(car => car.id === this.formCar.id);
            if (index !== -1) {
              this.cars[index] = response.data.message;
            }
          } else {
            this.cars.push(response.data.message);
          }
        } else {
          this.showSnackbarMessage(response.data.message, 'error');
        }
        this.closeCarModal();
      } catch (error) {
        console.error('Error saving car:', error);
        this.showSnackbarMessage(error.response.data.message, 'error');
      }
    },
    showParts(car) {
      this.selectedCar = car;
      this.fetchPartsForCar(car.id);
    },
    openPartModal(part) {
      if (part) {
        this.editPart = true;
        this.formPart = {...part};
      } else {
        this.editPart = false;
        this.formPart = {name: '', serialnumber: '', car_id: this.selectedCar.id};
      }
      this.isPartModalOpen = true;
    },
    closePartModal() {
      this.isPartModalOpen = false;
      this.$refs.partForm.reset();
    },
    async savePart() {
      if (!this.$refs.partForm.validate()) return;
      let response = null;

      try {
        if (this.editPart) {
          response = await axios.put(`http://127.0.0.1:8000/api/parts/${this.formPart.id}`, this.formPart);
        } else {
          response = await axios.post('http://127.0.0.1:8000/api/parts', this.formPart);
        }

        if (response && response.data.success) {
          this.showSnackbarMessage('Operation successful', 'success');
          if (this.editPart) {
            const index = this.selectedCar.parts.findIndex(part => part.id === this.formPart.id);
            if (index !== -1) {
              this.selectedCar.parts[index] = response.data.message;
            }
          } else {
            this.selectedCar.parts.push(response.data.message);
          }
        } else {
          this.showSnackbarMessage(response.data.message, 'error');
        }

        this.closePartModal();
      } catch (error) {
        console.error('Error saving part:', error);
        this.showSnackbarMessage(error.response.data.message, 'error');
      }
    },
    openDeleteDialog(type, item) {
      this.deleteType = type;
      this.itemToDelete = item;
      this.isDeleteDialogOpen = true;
    },
    closeDeleteDialog() {
      this.isDeleteDialogOpen = false;
      this.itemToDelete = null;
    },
    async confirmDelete() {
      try {
        let response = null;
        if (this.deleteType === 'car') {
          response = await axios.delete(`http://127.0.0.1:8000/api/cars/${this.itemToDelete.id}`);
          await this.fetchCars();
          this.selectedCar = null;
        } else if (this.deleteType === 'part') {
          response = await axios.delete(`http://127.0.0.1:8000/api/parts/${this.itemToDelete.id}`);
          await this.fetchPartsForCar(this.selectedCar.id);
        } else {
          this.showSnackbarMessage('Invalid delete type', 'error');
          return;
        }

        let messageType = response.data.success ? 'success' : 'error';
        this.showSnackbarMessage(response.data.message, messageType);

        this.closeDeleteDialog();
      } catch (error) {
        console.error(`Error deleting ${this.deleteType}:`, error);
        this.showSnackbarMessage(`Failed to delete ${this.deleteType}`, 'error');
      }
    },
    showSnackbarMessage(message, color) {
      this.snackbarMessage = message;
      this.snackbarColor = color;
      this.showSnackbar = true;
    },
    sortCars(key) {
      if (this.carSortKey === key) {
        this.carSortOrder *= -1;
      } else {
        this.carSortKey = key;
        this.carSortOrder = 1;
      }
    },
    sortParts(key) {
      if (this.partSortKey === key) {
        this.partSortOrder *= -1;
      } else {
        this.partSortKey = key;
        this.partSortOrder = 1;
      }
    },
    sortIcon(key, type = 'cars') {
      const sortKey = type === 'parts' ? this.partSortKey : this.carSortKey;
      const sortOrder = type === 'parts' ? this.partSortOrder : this.carSortOrder;

      if (sortKey !== key) return '';
      return sortOrder === 1 ? 'mdi-arrow-up' : 'mdi-arrow-down';
    },
  },
  computed: {
    filteredAndSortedCars() {
      return this.cars
        .filter(car => car.name.toLowerCase().includes(this.carSearchQuery.toLowerCase()))
        .slice()
        .sort((a, b) => {
          if (!this.carSortKey) return 0;
          const keyA = a[this.carSortKey];
          const keyB = b[this.carSortKey];
          if (keyA < keyB) return -1 * this.carSortOrder;
          if (keyA > keyB) return this.carSortOrder;
          return 0;
        });
    },
    filteredAndSortedParts() {
      return (this.selectedCar?.parts || [])
        .filter(part => part.name.toLowerCase().includes(this.partSearchQuery.toLowerCase()))
        .slice()
        .sort((a, b) => {
          if (!this.partSortKey) return 0;
          const keyA = a[this.partSortKey];
          const keyB = b[this.partSortKey];
          if (keyA < keyB) return -1 * this.partSortOrder;
          if (keyA > keyB) return this.partSortOrder;
          return 0;
        });
    },
  },
  mounted() {
    this.fetchCars();
  },
}
</script>

