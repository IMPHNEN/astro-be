import pino from 'pino';
import fs from 'fs';
import path from 'path';

const logDir = path.join(__dirname, 'log');
if (!fs.existsSync(logDir)) {
    fs.mkdirSync(logDir);
}

const log = pino({
    level: 'info',
    transport: {
        target: 'pino-pretty',
        options: {
            colorize: true,
        },
    },
}, pino.multistream([
    {
        level: 'error',
        stream: fs.createWriteStream(path.join(logDir, 'error.log'), { flags: 'a' }),
    },
    {
        level: 'fatal',
        stream: fs.createWriteStream(path.join(logDir, 'fatal.log'), { flags: 'a' }),
    },
    {
        level: 'warn',
        stream: fs.createWriteStream(path.join(logDir, 'warn.log'), { flags: 'a' }),
    }
]));

export default log;