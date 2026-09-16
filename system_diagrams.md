# System Design Diagrams - Go Umroh

Berikut adalah diagram-diagram rancangan sistem untuk aplikasi Go Umroh. Semua diagram digenerate menggunakan **Mermaid.js**.

## 1. Use Case Diagram

Diagram ini menunjukkan interaksi antara aktor (Customer dan Admin) dengan sistem Go Umroh.

```mermaid
flowchart LR
    Customer([Customer])
    Merchant([Merchant / Travel])
    Admin([Admin Utama])

    subgraph "Sistem Go Umroh"
        UC1(Lihat & Cari Paket Umroh)
        UC2(Lihat Detail Paket)
        UC3(Registrasi & Login)
        UC4(Pesan Paket Umroh / Booking)
        UC5(Input Data Jamaah)
        UC6(Konfirmasi Pembayaran)
        UC7(Lihat Riwayat Pesanan)

        UC_M1(Kelola Paket Umroh Sendiri)
        UC_M2(Kelola & Pantau Pesanan Travel)
        UC_M3(Kelola Profil Bisnis)

        UC8(Kelola Master Data Sistem)
        UC9(Pantau Seluruh Paket Umroh)
        UC10(Kelola Seluruh Pesanan & Validasi)
        UC12(Kelola Konten & Master Data)
        UC13(Kelola Pengguna & Verifikasi Merchant)
    end

    Customer --> UC1
    Customer --> UC2
    Customer --> UC3
    Customer --> UC4
    Customer --> UC5
    Customer --> UC6
    Customer --> UC7

    Merchant --> UC3
    Merchant --> UC_M1
    Merchant --> UC_M2
    Merchant --> UC_M3

    Admin --> UC3
    Admin --> UC8
    Admin --> UC9
    Admin --> UC10
    Admin --> UC12
    Admin --> UC13

    UC4 -. "<<include>>" .-> UC3
    UC6 -. "<<extend>>" .-> UC4
```

*(Catatan: Mermaid.js belum memiliki dukungan bawaan yang sempurna untuk Use Case diagram standar UML. Namun, diagram ER dan Activity di bawah ini jauh lebih detail).*

---

## 2. Entity Relationship Diagram (ERD)

Diagram ini menunjukkan struktur tabel database dan relasi antar entitas utama di sistem.

```mermaid
erDiagram
    USERS ||--o{ ORDERS : "places"
    USERS {
        bigint id PK
        string name
        string email
        string password
        string phone
        string address
    }

    UMRAH_PACKAGES ||--o{ ORDERS : "booked_in"
    UMRAH_PACKAGES ||--o{ UMRAH_PACKAGE_IMAGES : "has"
    UMRAH_PACKAGES }o--|| AIRLINES : "uses"
    UMRAH_PACKAGES }o--|| ROUTES : "follows"
    UMRAH_PACKAGES {
        bigint id PK
        string title
        string type "VIP/Promo/Regular"
        decimal price
        int capacity
        date departure_date
        int duration
    }

    UMRAH_PACKAGES ||--|{ UMRAH_PACKAGE_HOTELS : "includes"
    HOTELS ||--|{ UMRAH_PACKAGE_HOTELS : "included_in"

    ORDERS ||--o{ ORDER_PARTICIPANTS : "has"
    ORDERS ||--o| PAYMENT_CONFIRMATIONS : "has"
    ORDERS {
        bigint id PK
        bigint user_id FK
        bigint umrah_package_id FK
        string order_number
        string status "pending/paid/approved/cancelled"
        decimal total_price
    }

    ORDER_PARTICIPANTS {
        bigint id PK
        bigint order_id FK
        string name
        string nik
        string passport_number
    }

    PAYMENT_CONFIRMATIONS {
        bigint id PK
        bigint order_id FK
        bigint bank_id FK
        decimal amount
        string proof_image
        string status
    }
```

---

## 3. Class Diagram

Diagram ini merepresentasikan struktur class (Model) pada backend Laravel beserta atribut utama dan metode relasinya.

```mermaid
classDiagram
    class User {
        +int id
        +string name
        +string email
        +orders()
    }

    class UmrahPackage {
        +int id
        +string title
        +string type
        +decimal price
        +int capacity
        +images()
        +airline()
        +route()
        +hotels()
    }

    class Order {
        +int id
        +string order_number
        +string status
        +decimal total_price
        +user()
        +package()
        +participants()
        +paymentConfirmation()
    }

    class OrderParticipant {
        +int id
        +string name
        +string nik
        +order()
    }

    class PaymentConfirmation {
        +int id
        +decimal amount
        +string proof_image
        +string status
        +order()
        +bank()
    }

    class MasterData {
        <<Interface>>
        +Hotel
        +AirLine
        +Route
        +Bank
    }

    User "1" --> "*" Order
    UmrahPackage "1" --> "*" Order
    Order "1" --> "*" OrderParticipant
    Order "1" --> "1" PaymentConfirmation
```

---

## 4. Activity Diagram (Alur Pemesanan Paket Umroh)

Diagram ini menjelaskan alur aktivitas seorang Customer dari mulai memilih paket hingga pesanan disetujui (Approved) oleh Admin.

```mermaid
stateDiagram-v2
    [*] --> CariPaket
    CariPaket --> PilihPaket: Customer melihat detail
    
    PilihPaket --> CekLogin: Klik Pesan (Book)
    
    state CekLogin <<choice>>
    CekLogin --> Login: Belum Login
    CekLogin --> InputDataJamaah: Sudah Login
    
    Login --> InputDataJamaah
    
    InputDataJamaah --> Checkout: Mengisi data diri jamaah
    Checkout --> PesananDibuat: Sistem generate Order Number (Status: Pending)
    
    PesananDibuat --> UploadBuktiBayar: Customer transfer & upload bukti
    UploadBuktiBayar --> TungguVerifikasi: Status: Menunggu Verifikasi
    
    TungguVerifikasi --> VerifikasiAdmin: Admin mengecek pembayaran
    
    state VerifikasiAdmin <<choice>>
    VerifikasiAdmin --> PesananDitolak: Bukti tidak valid
    VerifikasiAdmin --> PesananDisetujui: Bukti valid (Status: Approved)
    
    PesananDitolak --> [*]
    PesananDisetujui --> [*]
```
