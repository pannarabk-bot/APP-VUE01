<template>
    <div class="container">
        <div class="row justify-content-center align-items-center vh-100">
            <div class="col-md-5 col-lg-4">

                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">

                        <h4 class="text-center mb-4">
                            🔐 เข้าสู่ระบบผู้ดูแล
                        </h4>

                        <!-- Username -->
                        <div class="mb-3">
                            <label class="form-label">ชื่อผู้ใช้</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input v-model="username" type="text" class="form-control" placeholder="กรอกชื่อผู้ใช้"
                                    @keyup.enter="login" />
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label class="form-label">รหัสผ่าน</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input v-model="password" type="password" class="form-control"
                                    placeholder="กรอกรหัสผ่าน" @keyup.enter="login" />
                            </div>
                        </div>

                        <!-- Button -->
                        <button @click="login" class="btn btn-primary w-100" :disabled="loading">
                            <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                            เข้าสู่ระบบ
                        </button>

                        <!-- Error -->
                        <div v-if="error" class="alert alert-danger mt-3 mb-0 text-center">
                            {{ error }}
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            username: "",
            password: "",
            error: "",
            loading: false,
        };
    },

    methods: {
   async login() {
    this.error = "";

    if (!this.username || !this.password) {
        this.error = "กรุณากรอกข้อมูลให้ครบ";
        return;
    }

    try {
        this.loading = true;

        const res = await axios.post(
            "http://localhost/APP-VUE01/php_api/login.php",
            {
                username: this.username,
                password: this.password,
            }
        );

        console.log(res.data);   // ✅ DEBUG ดูค่าจริง

        if (res.data.success) {

            localStorage.setItem("adminLogin", "true");
            localStorage.setItem("user", JSON.stringify(res.data.user));

            this.$router.push("/Product_crud");

        } else {

            // 🔥 ตรงนี้ที่หายไป
            this.error = res.data.message;

        }

    } catch (err) {
        this.error = "เกิดข้อผิดพลาดในการเชื่อมต่อ Server";
    } finally {
        this.loading = false;
    }
}
    },
};
</script>