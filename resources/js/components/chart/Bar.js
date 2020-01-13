import { Bar, mixins } from "vue-chartjs";
const { reactiveProp } = mixins;

export default {
    extends: Bar,
    mixins: [reactiveProp],
    props: ["chartData", "options", "dataOriginal"],
    watch: {
        dataOriginal() {
            this.renderChart(this.chartData, this.options);
        }
    },
    mounted() {
        this.renderChart(this.chartData, this.options);
    },
    beforeDestroy() {
        if (this._chart) {
            this._chart.destroy();
        }
    }
};
