<template>
  <div style="height:400px;">
    <Line v-if="chartData" :data="chartData" :options="chartOptions" />
  </div>
</template>

<script>
import { Line } from 'vue-chartjs' // ✅ Change Bar to Line
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  PointElement, // ✅ Add PointElement
  LineElement, // ✅ Add LineElement
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
  name: 'ChartSample',
  components: { Line }, // ✅ Change Bar to Line
  data() {
    return {
      chartData: null,
      chartOptions: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          title: {
            display: true,
            text: 'Population',
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
      const res = await axios.get('/api/inprofile/economicIndicator/1')
      const apiData = res.data

      if (apiData && apiData.datasets && Array.isArray(apiData.datasets)) {
        const populationDataset = apiData.datasets.find(
          (dataset) => dataset.label === 'Population'
        )

        if (populationDataset) {
          // ✅ Add a line-specific background color and border color
          populationDataset.backgroundColor = 'rgba(21, 76, 121, 0.5)' 
          populationDataset.borderColor = '#154c79' 
          populationDataset.fill = true // Fills the area under the line

          this.chartData = {
            labels: apiData.labels,
            datasets: [populationDataset],
          }
        } else {
          console.error('Population dataset not found in API response.')
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