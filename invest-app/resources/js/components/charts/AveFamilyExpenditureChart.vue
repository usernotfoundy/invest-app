<template>
  <div style="height:400px;">
    <Line v-if="chartData" :data="chartData" :options="chartOptions" />
  </div>
</template>

<script>
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  PointElement, 
  LineElement, 
  CategoryScale,
  LinearScale,
  Filler,
} from 'chart.js'
import axios from 'axios'

// Register all required components and plugins for a Line chart
ChartJS.register(
  Title,
  Tooltip,
  Legend,
  PointElement,
  LineElement,
  CategoryScale,
  LinearScale,
  Filler
)

export default {
  name: 'Average Family Expenditure',
  components: { Line },
  data() {
    return {
      chartData: null,
      chartOptions: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          title: {
            display: true,
            text: 'Average Family Expenditure',
          },
          legend: {
            display: false,
          },
        },
      },
    }
  },
  async mounted() {
    try {
      const res = await axios.get('/api/inprofile/economicIndicator')
      const apiData = res.data

      if (apiData && apiData.datasets && Array.isArray(apiData.datasets)) {
        const averageFamilyExpenditureDataset = apiData.datasets.find(
          (dataset) => dataset.label === 'Average Family Expenditure'
        )

        if (averageFamilyExpenditureDataset) {
          averageFamilyExpenditureDataset.backgroundColor = 'rgba(21, 76, 121, 0.5)' 
          averageFamilyExpenditureDataset.borderColor = '#154c79' 
          averageFamilyExpenditureDataset.fill = true

          this.chartData = {
            labels: apiData.labels,
            datasets: [averageFamilyExpenditureDataset],
          }
        } else {
          console.error('Average Family Expenditure dataset not found in API response.')
          this.chartData = null
        }
      } else {
        console.error('Invalid or missing datasets array in API response.')
        this.chartData = null
      }
    } catch (err) {
      console.error('Failed to load chart data', err)
      this.chartData = null
    }
  },
}
</script>