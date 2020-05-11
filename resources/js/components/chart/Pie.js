import { Pie, mixins } from "vue-chartjs";
const { reactiveProp } = mixins;

export default {
    extends: Pie,
    mixins: [reactiveProp],
    props: ["chartData"],
    watch: {},
    data: () => ({
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    }),

    mounted() {
        console.log(this.chartData);
        this.renderChart(this.chartData, this.options);
    }
};
