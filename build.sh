# Build angular app for logs and copy static files
cd angular/logs
npm install
ng build --configuration production
cd ../..
cp angular/logs/dist/logs/browser/main*.js resources/angular/logs/main.js
cp angular/logs/dist/logs/browser/polyfills*.js resources/angular/logs/polyfills.js
cp angular/logs/dist/logs/browser/scripts*.js resources/angular/logs/scripts.js
cp angular/logs/dist/logs/browser/styles*.css resources/angular/logs/styles.css

# Build angular app for accounting and copy static files
cd angular/accounting
npm install
ng build --configuration production
cd ../..
cp angular/accounting/dist/accounting/browser/main*.js resources/angular/accounting/main.js
cp angular/accounting/dist/accounting/browser/polyfills*.js resources/angular/accounting/polyfills.js
cp angular/accounting/dist/accounting/browser/scripts*.js resources/angular/accounting/scripts.js
cp angular/accounting/dist/accounting/browser/styles*.css resources/angular/accounting/styles.css

# Build angular app for payment and copy static files
cd angular/payment
npm install
ng build --configuration production
cd ../..
cp angular/payment/dist/payment/browser/main*.js resources/angular/payment/main.js
cp angular/payment/dist/payment/browser/polyfills*.js resources/angular/payment/polyfills.js
cp angular/payment/dist/payment/browser/scripts*.js resources/angular/payment/scripts.js
cp angular/payment/dist/payment/browser/styles*.css resources/angular/payment/styles.css

# Build angular app for attendance and copy static files
cd angular/attendance
npm install
ng build --configuration production
cd ../..
cp angular/attendance/dist/attendance/browser/main*.js resources/angular/attendance/main.js
cp angular/attendance/dist/attendance/browser/polyfills*.js resources/angular/attendance/polyfills.js
cp angular/attendance/dist/attendance/browser/scripts*.js resources/angular/attendance/scripts.js
cp angular/attendance/dist/attendance/browser/styles*.css resources/angular/attendance/styles.css

# Build Vite resources
npm run build
