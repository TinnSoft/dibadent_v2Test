import Vue from "vue";
import { HasError, AlertError, AlertSuccess } from "vform";
import kSelectFilter from "./kSelectFilter.vue";
import kBlockQuote from "./kBlockQuote.vue";
import kAttachFiles from "./kAttachFiles.vue";
import kSendEmailForm from "./kSendEmailForm.vue";
import kStatus from "./kStatus.vue";
import kCard from "./kCard.vue";

// Components that are registered globaly.
[
    kCard,
    HasError,
    AlertError,
    AlertSuccess,
    kSelectFilter,
    kBlockQuote,
    kAttachFiles,
    kSendEmailForm,
    kStatus,
].forEach(Component => {
    Vue.component(Component.name, Component);
});
