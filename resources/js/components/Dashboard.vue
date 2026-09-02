<template>
  <div class="max-w-5xl mx-auto py-10 px-5">
    <h1 class="text-3xl font-bold text-center mb-10 text-blue-600">Mini IELTS Speaking Evaluation</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <!-- Kolom Kiri: Form Latihan -->
      <div class="bg-white p-6 rounded-lg shadow-md h-fit">
        <h2 class="text-xl font-semibold mb-4">Practice Now</h2>
        
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Select Question</label>
          <select v-model="form.question_id" class="w-full border p-2 rounded">
            <option disabled value="">-- Choose a topic --</option>
            <option v-for="q in questions" :key="q.id" :value="q.id">
              Part {{ q.part }} - {{ q.topic }}
            </option>
          </select>
        </div>

        <div v-if="selectedQuestion" class="mb-4 p-4 bg-blue-50 rounded italic text-gray-700">
          "{{ selectedQuestion.prompt }}"
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Your Answer</label>
          <textarea v-model="form.answer" rows="5" class="w-full border p-2 rounded" placeholder="Type your answer here..."></textarea>
        </div>

        <button @click="submitAnswer" :disabled="loading" class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 disabled:opacity-50">
          {{ loading ? 'Evaluating with AI...' : 'Submit Answer' }}
        </button>
      </div>

      <!-- Kolom Kanan: Riwayat Evaluasi -->
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">Your Evaluation History</h2>
        
        <div v-if="attempts.length === 0" class="text-gray-500 text-center py-4">
          No attempts yet. Start practicing!
        </div>

        <div v-else class="space-y-6 max-h-[600px] overflow-y-auto pr-2">
          <div v-for="attempt in attempts" :key="attempt.id" class="border-b pb-4">
            <div class="flex justify-between items-start mb-2">
              <span class="font-bold text-gray-800">{{ attempt.question.topic }}</span>
              <span class="bg-blue-100 text-blue-800 font-bold px-3 py-1 rounded-full">Band: {{ attempt.band_score }}</span>
            </div>
            <p class="text-sm text-gray-600 italic mb-3">"{{ attempt.answer }}"</p>
            
            <div class="grid grid-cols-1 gap-2 text-sm">
              <div class="bg-green-50 p-2 rounded border border-green-200">
                <strong class="text-green-700">Strengths:</strong> {{ attempt.strengths }}
              </div>
              <div class="bg-yellow-50 p-2 rounded border border-yellow-200">
                <strong class="text-yellow-700">To Improve:</strong> {{ attempt.improvements }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const questions = ref([]);
const attempts = ref([]);
const loading = ref(false);

const form = ref({
  question_id: '',
  answer: ''
});

const selectedQuestion = computed(() => {
  return questions.value.find(q => q.id === form.value.question_id);
});

const fetchData = async () => {
  const [qRes, aRes] = await Promise.all([
    axios.get('/api/speaking/questions'),
    axios.get('/api/speaking/attempts')
  ]);
  questions.value = qRes.data;
  attempts.value = aRes.data;
};

const submitAnswer = async () => {
  if (!form.value.question_id || form.value.answer.trim().length < 5) {
    alert("Please select a question and write a valid answer.");
    return;
  }

  loading.value = true;
  try {
    await axios.post('/api/speaking/submit', form.value);
    form.value.answer = ''; // Reset form
    await fetchData(); // Refresh data
  } catch (error) {
    alert("Error evaluating answer. Check console.");
    console.error(error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchData();
});
</script>