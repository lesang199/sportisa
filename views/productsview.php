<style>
        .sidebar-list li a {
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            display: block;
            padding: 0.75rem 1rem;
            border-radius: 0.25rem;
        }
        .sidebar-list li a:hover {
            background-color: #f8f9fa;
            color: #0d6efd;
        }
        .sidebar-list li a.active {
            background-color: #e9ecef;
            color: #0d6efd;
            font-weight: 600;
        }
        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .form-check-label {
            cursor: pointer;
            transition: color 0.5s ease;
        }
        .form-check:hover .form-check-label {
            color: #0d6efd;
        }
        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        
        .product-card:hover .card-img-top {
            transform: scale(1.05);
        }
        
        .product-actions {
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .product-card:hover .product-actions {
            opacity: 1;
        }
        
        .product-image-container {
            background-color: #f8f9fa;
        }
    </style>