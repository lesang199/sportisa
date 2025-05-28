<style>
        /* Header */
        header{
          position: fixed;
          width: 100%;
          z-index: 20;
        }
 
        /* Hero */
        .hero{
            position: relative;
            top: 47px;
        }
        /* Cources */
      .carousel-container {
        width: 100%;
        height: 480px;
        background-color: aliceblue;
        position: relative;
        overflow: hidden;
        top: 50px;
         display: flex;
       justify-content: center;
       align-items: center;
   background-color: #378d570f;
   }

.carousel-img {
  display: flex;
  transition: transform 0.5s ease;
  width: 100%;
  height: 100%;
}

.carousel-img img {
  width: 100%;
  height: 100%;
  flex-shrink: 0;
  object-fit:fill;
}

.arrow-icon-left,
.arrow-icon-right {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  z-index: 10;
  background-color: rgba(0, 0, 0, 0.5);
  padding: 10px;
  border-radius: 50%;
  transition: 0.3s;
}
.carousel-inner-custom {
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 90%;
  height: 100%;
  overflow: hidden;
}

.arrow-icon-left:hover,
.arrow-icon-right:hover {
  background-color: rgba(0, 0, 0, 0.8);
  transform: translateY(-50%) scale(1.1);
}

.arrow-icon-left {
  left: 10px;
}

.arrow-icon-right {
  right: 10px;
}

.arrow-icon-left i,
.arrow-icon-right i {
  font-size: 24px;
  color: white;
}
/* Features */
.features h2{
  padding-top: 50px;
}

/* Brands */
.card {
  position: relative;
  overflow: hidden;
}

.card-body.overlay {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: rgba(0, 0, 0, 0.5); /* Nền mờ */
  color: white;
  padding: 10px;
  border-radius: 5px;
  opacity: 0;
  transition: opacity 0.3s ease;
  text-align: center;
}

.card:hover .card-body.overlay {
  opacity: 1;
}
</style>