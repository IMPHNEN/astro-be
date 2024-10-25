import * as dotenv from "dotenv";

dotenv.config()


export default {
    mode: process.env.NODE_ENV || "development",
    uploadPath: process.env.UPLOAD_PATH || "uploads",
    port: process.env.PORT || 3000,
}