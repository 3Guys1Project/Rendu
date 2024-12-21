import { reactive } from 'vue';
import { EventService, SportType } from '@/lib/services/event.service';
import { userStore } from '@/stores/user.store.ts';

type State = {
	events: SportType[];
	loading: boolean;
};

type Actions = {
	setEvents: (events: SportType[]) => void;
	addEvent: (event: SportType) => void;
	loadEvents: () => Promise<void>;
	getEventById: (id: string) => Promise<SportType | null>;
	participate: (id: number) => void;
	removeParticipation: (id: number) => void;
	createEvent: (eventData: Partial<SportType>) => Promise<SportType | null>;
};

export const eventStore = reactive<State & Actions>({
	events: [],
	loading: false,

	setEvents(events: SportType[]) {
		this.events = events;
	},

	addEvent(event: SportType) {
		this.events.push(event);
	},

	async loadEvents() {
		this.loading = true;
		const data = await EventService.getAll();
		if (data) {
			this.setEvents(data);
		}
		this.loading = false;
	},

	async getEventById(id: string): Promise<SportType | null> {
		this.loading = true;
		const existingEvent = this.events.find((event) => event.id.toString() === id);
		if (existingEvent) {
			this.loading = false;
			return existingEvent;
		}

		const event = await EventService.get(id);
		if (event) {
			this.addEvent(event);
		}
		this.loading = false;
		return event;
	},

	participate(id: number) {
		this.events = this.events.map((event) => {
			if (event.id === id) {
				event.participating = true;
				const user = userStore.getUser();
				if (user && event.participants_visible) {
					event.participations.push({ user });
				}
			}
			return event;
		});
	},

	removeParticipation(id: number) {
		this.events = this.events.map((event) => {
			if (event.id === id) {
				event.participating = false;
				const user = userStore.getUser();
				if (user) {
					event.participations = event.participations.filter(
						(participation) => participation.user.id !== user.id
					);
				}
			}
			return event;
		});
	},

	async createEvent(eventData: Partial<SportType>): Promise<SportType | null> {
		const newEvent = await EventService.create(eventData);
		if (newEvent) {
			this.addEvent(newEvent);
			return newEvent;
		}
		return null;
	},
});



export const sports: string[] = [
	'Football',
	'Rugby',
	'Handball',
	'Volleyball',
	'Badminton',
	'Tennis',
	'Natation',
	'Basketball',
	'Cycling',
	'Running',
	'Swimming',
	'Boxing',
	'Wrestling',
	'Judo',
	'Karate',
	'Taekwondo',
	'Fencing',
	'Archery',
	'Shooting',
	'Rowing',
	'Canoeing',
	'Sailing',
	'Equestrian',
	'Gymnastics',
	'Weightlifting',
	'Triathlon',
	'Pentathlon',
	'Golf',
	'Baseball',
	'Softball',
	'Cricket',
	'Hockey',
	'Table Tennis',
	'Squash',
	'Surfing',
	'Skateboarding',
	'Climbing',
	'BMX',
	'Mountain Biking',
	'Snowboarding',
	'Skiing',
	'Ice Hockey',
	'Figure Skating',
	'Speed Skating',
	'Curling',
	'Luge',
	'Bobsleigh',
	'Skeleton'
];