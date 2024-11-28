class Observer {
    constructor(name) {
      this.name = name;
    }
  
    update(data) {
      console.log(`Notification for ${this.name}: ${data.message}`);
    }
  }
  