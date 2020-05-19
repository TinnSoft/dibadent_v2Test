<template>
  <div class="WAL position-relative bg-grey-3" :style="style">
    <q-layout view="lHh Lpr lFf" class="WAL__layout shadow-3" container>
      <q-header elevated>
        <q-toolbar class="bg-grey-3 text-black">
          <q-btn
            round
            flat
            icon="keyboard_arrow_left"
            class="WAL__drawer-open q-mr-sm"
            @click="leftDrawerOpen = true"
          />

          <q-btn round flat>
            <q-avatar>
              <img :src="currentConversation.avatar" />
            </q-avatar>
          </q-btn>

          <span class="q-subtitle-1 q-pl-md">{{ currentConversation.user_name }}</span>
          <q-space />
          <div v-if="$q.platform.is.mobile">
            <q-btn round flat icon="home" to="/" />
          </div>
        </q-toolbar>
      </q-header>

      <q-drawer v-model="leftDrawerOpen" show-if-above bordered :breakpoint="690">
        <q-toolbar class="bg-grey-3">
          <q-space />
          <q-btn round flat icon="refresh" @click="fetchData()" />
          <q-btn round flat icon="close" class="WAL__drawer-close" @click="leftDrawerOpen = false" />
        </q-toolbar>

        <q-scroll-area style="height: calc(100% - 100px)">
          <q-list>
            <q-item
              v-for="(conversation, index) in conversations"
              :key="conversation.id"
              clickable
              v-ripple
              @click="currentConversationIndex = index"
            >
              <q-item-section avatar>
                <q-avatar>
                  <img :src="conversation.avatar" />
                </q-avatar>
              </q-item-section>

              <q-item-section>
                <q-item-label lines="1">
                  {{ conversation.user_name }}
                  <q-badge v-if="conversation.daysDifference<=3" color="blue" transparent>
                    <q-icon name="chat" color="white"></q-icon>
                  </q-badge>
                </q-item-label>
                <q-item-label class="conversation__summary" caption>{{ conversation.date }}</q-item-label>
              </q-item-section>

              <q-item-section side>
                <q-item-label caption>{{ conversation.time }}</q-item-label>
                <q-icon name="keyboard_arrow_down" />
              </q-item-section>
            </q-item>
          </q-list>
        </q-scroll-area>
      </q-drawer>

      <q-page-container class="bg-grey-2">
        <router-view />
        <div
          v-for="(chathistory) in conversations[currentConversationIndex].chat_history"
          :key="chathistory.id"
        >
          <q-chat-message
            ref="qchatx"
            :name="chathistory.who"
            :avatar="chathistory.who == 'Yo' ? avatarMe : conversations[currentConversationIndex].avatar"
            :text="[chathistory.comment]"
            :stamp="chathistory.date"
            :bg-color="chathistory.bgcolor"
            :sent="chathistory.who == 'Yo' ? true : false"
          ></q-chat-message>
        </div>
      </q-page-container>

      <q-footer>
        <q-toolbar class="bg-grey-3 text-black row">
          <q-input
            rounded
            outlined
            dense
            class="WAL__field col-grow q-mr-sm"
            bg-color="white"
            v-model="messageToSave.comment"
            placeholder="Escribe un mensaje"
            @keyup.enter="saveMessage()"
          />
        </q-toolbar>
      </q-footer>
    </q-layout>
  </div>
</template>

<script>
import kNotify from "../../components/messages/Notify.js";

export default {
  middleware: "auth",
  name: "WhatsappLayout",
  data() {
    return {
      leftDrawerOpen: false,
      chatDoctorspath: "getAllDoctorsChats",
      sentx: true,
      search: "",
      currentConversationIndex: 0,
      conversations: [{}],
      avatarMe: null,
      messageToSave: {
        comment: "",
        doctor_id_parent: null
      },
      loading: false
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    saveMessage() {
      let vm = this;
      vm.loading = true;
      if (vm.messageToSave.comment) {
        vm.$set(
          vm.messageToSave,
          "doctor_id_parent",
          vm.currentConversation.id
        );
        axios
          .post("/api/chats/", vm.messageToSave)
          .then(function(response) {
            if (response.data.created) {
              vm.$set(vm.messageToSave, "comment", null);
            }
            vm.fetchData();
            vm.loading = false;
          })
          .catch(function(error) {
            kNotify(
              vm,
              "OOPS! no fue posible guardar tu mensaje... Intente de nuevo",
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
        .get(`/api/${vm.chatDoctorspath}`)
        .then(function(response) {
          console.log(response.data);
          if (response.data.AllDoctorsChats) {
            vm.$set(vm, "conversations", response.data.AllDoctorsChats);
          }
          if (response.data.avatarMe[0].avatar) {
            vm.$set(vm, "avatarMe", response.data.avatarMe[0].avatar);
          }

          vm.loading = false;
        })
        .catch(function(error) {
          vm.loading = false;
        });
    }
  },
  computed: {
    currentConversation() {
      return this.conversations[this.currentConversationIndex];
    },
    style() {
      return {
        height: this.$q.screen.height + "px"
      };
    }
  }
};
</script>

<style lang="sass">
.WAL
  width: 100%
  height: 100%
  padding-top: 20px
  padding-bottom: 20px
  &:before
    content: ''
    height: 127px
    top: 0
    width: 100%
  &__layout
    margin: 0 auto
    z-index: 4000
    height: 90%
    width: 90%
    max-width: 950px
    border-radius: 5px
  &__field.q-field--outlined .q-field__control:before
    border: none
  .q-drawer--standard
    .WAL__drawer-close
      display: none
@media (max-width: 850px)
  .WAL
    padding: 0
    &__layout
      width: 100%
      border-radius: 0
@media (min-width: 691px)
  .WAL
    &__drawer-open
      display: none
.conversation__summary
  margin-top: 4px
.conversation__more
  margin-top: 0!important
  font-size: 1.4rem
</style>