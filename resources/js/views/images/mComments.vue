<template>
  <div class="q-pa-md">
    <div class="q-gutter-sm">
      <q-dialog v-model="showComments" persistent @hide="handleHide">
        <q-card style="width: 800px; max-width: 90vw;">
          <q-bar class="bg-blue text-white">
            <q-icon name="mail"></q-icon>
            <div>{{toolbarLabel}}</div>

            <q-space></q-space>

            <q-btn dense flat icon="close" v-close-popup>
              <q-tooltip>Cerrar</q-tooltip>
            </q-btn>
          </q-bar>

          <q-card-section>
            <q-input
              clearable
              v-model="varComments.comment"
              filled
              autogrow
              label="Agregar comentarios sobre la imagen"
              lazy-rules
            >
              <template v-slot:after>
                <q-btn label="AGREGAR" glossy stack icon="add" color="primary" @click="addComment" />
              </template>
            </q-input>
          </q-card-section>

          <q-card-section>
            <q-timeline layout="dense" v-for="(commentRetrieved, i) in retrievedComments" :key="i" color="primary">
              <q-timeline-entry
                :title="commentRetrieved.title"
                :subtitle="commentRetrieved.created_at"
                icon="comment"
              >
                <div>{{commentRetrieved.comment}}</div>
              </q-timeline-entry>
            </q-timeline>
          </q-card-section>
          <q-inner-loading :showing="loading">
            <q-spinner-gears size="50px" color="primary" />
          </q-inner-loading>
        </q-card>
      </q-dialog>
    </div>
  </div>
</template>

<script>
import kNotify from "../../components/messages/Notify.js";

export default {
  data() {
    return {
      showComments: false,
      toolbarLabel: "COMENTARIOS",
      imageAttributes: [],
      varComments: {
        image_id: null,
        comment: null
      },
      retrievedComments: [],
      loading: false
    };
  },
  methods: {
    addComment() {
      let vm = this;
      vm.loading = true;
      if (vm.varComments.comment) {
        vm.$set(vm.varComments, "image_id", vm.imageAttributes.id);

        axios
          .post("/api/comments/", vm.varComments)
          .then(function(response) {
            vm.fetchData();
            vm.varComments.comment = null;
            kNotify(vm, "Se guardó su comentario", "positive");

            vm.loading = false;
          })
          .catch(function(error) {
            kNotify(
              vm,
              "OOPS! no fue posible guardar tu comentario... Intente de nuevo",
              "negative"
            );
            vm.loading = false;
          });
      }
    },
    async fetchData() {
      let vm = this;
      vm.loading = true;
      axios
        .get(`/api/get_CommentsByImageId/${vm.imageAttributes.id}`)
        .then(function(response) {
          if (response.data.comments) {
            vm.$set(vm, "retrievedComments", response.data.comments);
          }
          vm.loading = false;
        })
        .catch(function(error) {
          vm.loading = false;
        });
    },
    open(imageAttributes) {
      let vm = this;
      vm.imageAttributes = imageAttributes;
      vm.varComments.comment = null;
      vm.retrievedComments = [];
      vm.fetchData();
      vm.showComments = true;
    },
    handleHide(newVal) {
      this.$emit("hide", this.imageAttributes);
    }
  }
};
</script>
