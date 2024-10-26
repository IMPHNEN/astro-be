
import express from "express";
import multer from "multer";
import config from "@config/config";
import log from "@utils/log";
import helmet from "helmet";
import { specs, swaggerUi } from "@docs/swagger";


// API ROUTES
import api from "@routes/api";

const app = express();
app.disable("x-powered-by");
app.use(helmet({
    xPoweredBy: false,
    noSniff: true,
    frameguard: true,
}));

app.use(express.static(config.uploadPath));
app.use('/docs', swaggerUi.serve, swaggerUi.setup(specs));


/**
 * @swagger
 * /sample:
 *   get:
 *     summary: Get app health
 *     responses:
 *       200:
 *         description: A successful response
 *         content:
 *           text/plain:
 *              example: Sun is shining!
 */
app.get('/', (req, res) => {
    res.send("Sun is shining!");
});

app.use("/api/v1", api);

app.get('/api-docs.json', (req, res) => {
    res.json(specs);
});

app.listen(config.port, () => {
    log.info(`Server started at http://localhost:${config.port}`);
})