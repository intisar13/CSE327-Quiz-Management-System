const { MongoClient, ServerApiVersion } = require("mongodb");

class Database {
  static instance = null;

  constructor() {
    if (Database.instance) {
      throw new Error("You can only create one instance of Database!");
    }

    this.client = null;
    this.db = null;
  }

  static getInstance() {
    if (!Database.instance) {
      Database.instance = new Database();
    }
    return Database.instance;
  }

  async connect(url, dbName) {
    if (!this.client) {
      this.client = new MongoClient(url, {
        serverApi: {
          version: ServerApiVersion.v1,
          strict: true,
          deprecationErrors: true,
        },
      });
      await this.client.connect();
      console.log("Connected to MongoDB");
    }
    if (!this.db) {
      this.db = this.client.db(dbName);
    }
    return this.db;
  }

  async disconnect() {
    if (this.client) {
      await this.client.close();
      console.log("Disconnected from MongoDB");
    }
    this.client = null;
    this.db = null;
  }
}

module.exports = Database;
