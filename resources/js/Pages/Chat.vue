<script setup>
</script>

<template>
  <AppLayout title="Chat">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Chat</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div
          class="bg-white overflow-hidden shadow-xl sm:rounded-lg flex"
          style="min-height: 400px; max-height: 400px"
        >
          <!-- list users -->
          <div
            class="w-3/12 bg-gray-200 bg-opacity-25 border-r border-gray-200 overflow-y-scroll"
          >
            <ul>
              <li
                v-for="user in users"
                :key="user.id"
                @click="loadMensagens(user.id)"
                :class="userChatId == user.id ? 'activeUser' : ''"
                class="p-6 text-gray-600 leading-7 font-semibold border-b border-gray-200 hover:bg-opacit-50 hover:cursor-pointer"
              >
                <p class="flex items-center">
                  {{ user.name }}
                  <small v-if="userNotifi[user.id]" class="ml-2 w-2 h-2 bg-blue-500 rounded-full">{{ userNotifi[user.id] }}</small>
                </p>
              </li>
            </ul>
          </div>
          <!-- box message -->
          <div class="w-9/12 flex flex-col justify-between">
            <div
              class="w-full p-6 flex flex-col overflow-y-scroll"
              ref="messagesContainer"
            >
              <div
                v-for="msg in mensagens"
                :key="msg.id"
                class="w-full mb-3 msg"
                :class="logAndCheckUserId(msg) ? 'text-right' : ''"
              >
                <p
                  class="inline-block p-2 rounded-md font-semibold"
                  style="max-width: 75%"
                  :class="logAndCheckUserId(msg) ? 'msgFromMe' : 'msgToMe'"
                >
                  {{ msg.mensagem }}
                </p>
                <small class="w-full">{{ msg.created_at }}</small>
              </div>
            </div>

            <div
              v-if="userChatId"
              class="w-full bg-gray-200 bg-opacity-25 p-6 border-t border-gray-200"
            >
              <form v-on:submit.prevent="sendMesage()">
                <div
                  class="flex rounded-md over-flow-hidden border border-gray-300"
                >
                  <input
                    type="text"
                    class="flex-1 px-4 py-2 text-sm focus:outline-none"
                    v-model="text"
                  />
                  <button
                    type="submit"
                    class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2"
                  >
                    Enviar
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.msgFromMe {
  background-color: #5cc4769f;
  color: #000 !important;
  opacity: 0.25;
}
.msgToMe {
  background-color: #2657a0b6;
  color: #000 !important;
  opacity: 0.25;
}
.activeUser {
  border-left: 2px solid blue;
}
</style>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import axios from "axios";
import moment from "moment";

export default {
  components: { AppLayout },
  data() {
    return {
      users: [],
      mensagens: [],
      userChatId: 0,
      text: "",
      userNotifi: []
    };
  },
  props: {
    auth: Object,
  },
  methods: {
    scrollToBottom() {
      if (this.mensagens.length > 0) {
        document.querySelectorAll(".msg:last-child")[0].scrollIntoView();
      }
    },
    async loadMensagens(user) {
      this.userChatId = user;
      await axios.get(`api/mensagens-list/${user}`).then((res) => {
        this.mensagens = res.data.msgs;
        this.mensagens.created_at = moment(this.mensagens.created_at).format(
          "DD/MM/YYYY HH:mm"
        );
      });

      this.userNotifi[this.userChatId] = 0;

      this.scrollToBottom();
    },
    logAndCheckUserId(msg) {
      return msg.de === this.auth.user.id;
    },
    sendMesage() {
      const data = {
        mensagem: this.text,
        para: this.userChatId,
      };

      axios
        .post("api/mensagens/store", data)
        .then(async (res) => {
          if (res.status == 201) {
            // this.loadMensagens(this.userChatId);
            await this.mensagens.push({
              de: this.auth.user.id,
              para: this.userChatId,
              mensagem: this.text,
              created_at: new Date().toISOString(),
            });
            this.text = "";
            this.scrollToBottom();
          }
        })
        .catch((err) => console.log(err));
    },
  },
  mounted() {
    axios.get("api/users").then((res) => {
      this.users = res.data.users;
    });

    Echo.private(`user.${this.auth.user.id}`).listen(".SendMessage", async (e) => {
        if (this.userChatId && e.menssage.de === this.userChatId) {
          await this.mensagens.push(e.menssage);
          this.scrollToBottom();
        }else{
          this.userNotifi[e.menssage.de] = (this.userNotifi[e.menssage.de] || 0) + 1;
        }
      }
    );
  },
};
</script>
