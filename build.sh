# Build angular app for payment and copy static files
cd angular/payment
ng build --configuration production
cd ../..
cp angular/payment/dist/payment/browser/main*.js resources/angular/payment/main.js
cp angular/payment/dist/payment/browser/polyfills*.js resources/angular/payment/polyfills.js
cp angular/payment/dist/payment/browser/scripts*.js resources/angular/payment/scripts.js
cp angular/payment/dist/payment/browser/styles*.css resources/angular/payment/styles.css

# Build angular app for attendance and copy static files
cd angular/attendance
ng build --configuration production
cd ../..
cp angular/attendance/dist/attendance/browser/main*.js resources/angular/attendance/main.js
cp angular/attendance/dist/attendance/browser/polyfills*.js resources/angular/attendance/polyfills.js
cp angular/attendance/dist/attendance/browser/scripts*.js resources/angular/attendance/scripts.js
cp angular/attendance/dist/attendance/browser/styles*.css resources/angular/attendance/styles.css

# Build Vite resources
npm run build
