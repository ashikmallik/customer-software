

<style>
.nav-bar {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  background: #f8f9fa;
  border: 1px solid #dee2e6;
  border-radius: 10px;
  padding: 10px 12px;
  align-items: center;
}
.nav-btn {
  padding: 8px 20px;
  border-radius: 8px;
  border: 1px solid #dee2e6;
  background: #fff;
  color: #212529;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: all 0.15s ease;
}
.nav-btn:hover {
  background: #e9ecef;
}
.nav-btn.active {
  background: #185FA5;
  color: #fff;
  border-color: #185FA5;
}
</style>

<!-- HTML -->
<div class="nav-bar">
  <a href="#" class="nav-btn active">Button 1</a>
  <a href="#" class="nav-btn">Button 2</a>
  <a href="#" class="nav-btn">Button 3</a>
  <a href="#" class="nav-btn">Button 4</a>
  <a href="#" class="nav-btn">Button 5</a>
</div>