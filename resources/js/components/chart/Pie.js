import { Pie, mixins } from "vue-chartjs";
const { reactiveProp } = mixins;

export default {
    extends: Pie,
    mixins: [reactiveProp],
    props: {
        chartdata: {
            type: Object,
            default: null
        },
        options: {
            type: Object,
            default: null
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
