import {AuthService} from '@/lib/services/auth.service.ts';

function removeTrailingSlash(url: string) {
	return url.replace(/\/$/, '');
}

type  OptionsType = {
	noCredentials?: boolean;
	refreshAllowed?: boolean;
}

async function request<T>(method: string, endpoint: string, data: any, opts: OptionsType): Promise<{
	success: boolean,
	status: number,
	data?: T,
	response?: Response,
	error?: Error
}> {
	try {
		const response = await fetch(`${removeTrailingSlash(import.meta.env.VITE_API)}/${removeTrailingSlash(endpoint)}`, {
			method,
			headers: {
				'Content-Type': 'application/json'
			},
			credentials: opts?.noCredentials ? 'omit' : 'include',
			body: data ? JSON.stringify(data) : undefined
		});
		if (response.status === 401 && opts.refreshAllowed) {
			const refreshSuccess = await AuthService.refreshToken();
			if (refreshSuccess) {
				return request<T>(method, endpoint, data, {...opts, refreshAllowed: false});
			}
		}
		return {
			success: response.ok,
			status: response.status,
			data: response.status === 204 ? undefined : await response.json(),
			response
		};
	} catch (error) {
		console.error(error);
		return {
			success: false,
			status: 0,
			error: error as Error
		};
	}
}

export async function get<T>(endpoint: string, opts?: OptionsType) {
	return request<T>('GET', endpoint, null, {
		refreshAllowed: true,
		...opts
	});
}

export async function post<T>(endpoint: string, data?: any, opts?: OptionsType) {
	return request<T>('POST', endpoint, data, {
		refreshAllowed: true,
		...opts
	});
}

export async function patch<T>(endpoint: string, data: any, opts?: OptionsType): Promise<{
	success: boolean,
	status: number,
	data?: T,
	error?: Error
}> {
	return request<T>('PATCH', endpoint, data, {
		refreshAllowed: true,
		...opts
	});
}

export async function del(endpoint: string, opts?: OptionsType): Promise<{
	success: boolean,
	status: number,
	error?: Error
}> {
	return request<null>('DELETE', endpoint, null, {
		refreshAllowed: true,
		...opts
	});
}

