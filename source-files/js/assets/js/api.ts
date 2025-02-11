import { debugConsole } from "./general";

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export const getMessage = async ({ url }: { url: string }): Promise<any> => {
    debugConsole.log('get message:', url)

    try {
        const response = await fetch(`${url}`);
        // https://sort.joinmerge.gr/api/v1/message

        // Check if the response is successful (status code 2xx)
        if (!response.ok || (response.status < 200 || response.status >= 400)) {
            throw new Error(`Request failed with status ${response.status}`);
        }
        debugConsole.log('response status:', response.status)

        // Parse the response (assuming it's JSON)
        const data = await response.json();

        return data;
    } catch (error) {
        debugConsole.log("Error occurred while fetching data:", (error as Error).message);
        // You can throw the error to be handled by the caller if needed
        // throw error;
    }
};

export const getMetaKeysProgress = async ({ url }: { url: string }): Promise<{
    nextPageToProcess: number;
} | undefined> => {
    debugConsole.log('get message:', url)
    try {
        const response = await fetch(`${url}`);
        // https://sort.joinmerge.gr/api/v1/message

        // Check if the response is successful (status code 2xx)
        if (!response.ok || (response.status < 200 || response.status >= 400)) {
            throw new Error(`Request failed with status ${response.status}`);
        }
        debugConsole.log('response status:', response.status)

        // Parse the response (assuming it's JSON)
        const data = await response.json();

        return data;
    } catch (error) {
        debugConsole.log("Error occurred while fetching data:", (error as Error).message);
        // You can throw the error to be handled by the caller if needed
        // throw error;
    }
};
