# 1. Gunakan base image Node.js versi 22 yang stabil untuk Angular 20
FROM node:22-alpine

# 2. Set environment variable agar terminal di dalam container berjalan lancar
ENV NODE_ENV=development

# 3. Tentukan folder kerja utama di dalam mesin virtual Docker
WORKDIR /app

# 4. Salin file package.json dan package-lock.json (jika ada) ke dalam Docker
COPY package*.json ./

# 5. Bersihkan cache dan install semua dependencies bawaan Angular 20 & Capacitor 8
RUN npm cache clean --force && npm install

# 6. Install Ionic CLI secara global di dalam container dengan versi yang stabil
RUN npm install -g @ionic/cli

# 7. Salin seluruh sisa kode program dari laptop kamu ke dalam folder kerja Docker
COPY . .

# 8. Buka akses port 8100 untuk jalur live reload browser laptopmu
EXPOSE 8100

# 9. Jalankan server lokal Ionic yang bisa diakses dari jaringan luar container
CMD ["ionic", "serve", "--host", "0.0.0.0", "--port", "8100"]