 -files .env                                        | 324 [32m++++++++++++++++++[m
 app/Http/Controllers/Admin/CustomerController.php  |  47 [32m++[m[31m-[m
 app/Http/Controllers/Admin/DashboardController.php |  59 [32m++[m[31m--[m
 app/Http/Controllers/Client/HomeController.php     |  52 [32m+[m[31m--[m
 app/Http/Controllers/Client/HotelController.php    |  60 [32m++[m[31m--[m
 .../Controllers/Client/PasswordResetController.php | 138 [32m++++++++[m
 .../Controllers/Client/ReservationController.php   |   4 [32m+[m[31m-[m
 app/Models/Customer.php                            |   3 [32m+[m
 app/Models/Hotel.php                               |  13 [32m+[m
 app/Models/Reservation.php                         |  50 [32m+++[m
 app/Models/Review.php                              |  10 [32m+[m
 app/Models/User.php                                |  17 [32m+[m[31m-[m
 .../ClientPasswordResetNotification.php            |  57 [32m++++[m
 .../ReservationStatusChangedNotification.php       | 101 [32m++++++[m
 ...19_142609_add_performance_indexes_to_tables.php |  85 [32m+++++[m
 final_report.md                                    |  32 [32m++[m
 resources/views/admin/customers/index.blade.php    |   8 [32m+[m
 resources/views/admin/reports/pdf.blade.php        |   2 [32m+[m[31m-[m
 .../views/client/auth/forgot-password.blade.php    | 254 [32m++++++++++++++[m
 .../views/client/auth/reset-password.blade.php     | 292 [32m++++++++++++++++[m
 resources/views/client/home.blade.php              |   4 [32m+[m[31m-[m
 resources/views/client/reservations/show.blade.php |  10 [32m+[m[31m-[m
 resources/views/emails/password_reset.blade.php    | 218 [32m++++++++++++[m
 .../views/emails/reservation_status.blade.php      | 308 [32m+++++++++++++++++[m
 resources/views/errors/403.blade.php               |   2 [32m+[m[31m-[m
 resources/views/super_admin/auth/login.blade.php   |   2 [32m+[m[31m-[m
 resources/views/welcome.blade.php                  |   2 [32m+[m[31m-[m
 routes/client.php                                  |   7 [32m+[m
 tatus --short                                      |  24 [32m++[m
 test.php                                           |   1 [32m+[m
 tests/Feature/ClientPasswordResetTest.php          | 379 [32m+++++++++++++++++++++[m
 tests/Feature/PerformanceOptimizationTest.php      | 212 [32m++++++++++++[m
 .../ReservationStatusEmailNotificationTest.php     | 285 [32m++++++++++++++++[m
 33 files changed, 2967 insertions(+), 95 deletions(-)
