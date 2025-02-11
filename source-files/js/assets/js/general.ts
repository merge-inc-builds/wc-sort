export const debugConsole = {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    log: (message: string, ...args: any[]) => {
        // eslint-disable-next-line @typescript-eslint/no-explicit-any
        if ((window as any)?.wc_sort_data?.dev) {
            console.log(message, ...args);
        }
    }
}