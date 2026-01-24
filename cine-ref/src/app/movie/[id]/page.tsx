import { notFound } from "next/navigation";

type MovieDetail = {
  id: number;
  title: string;
  poster_path: string;
  release_date: string;
  overview: string;
  genres: { id: number; name: string }[];
};

async function getMovie(id: string): Promise<MovieDetail | null> {
  const res = await fetch(
    `https://api.themoviedb.org/3/movie/${id}?api_key=${process.env.TMDB_API_KEY}&language=es-ES`,
    { cache: "no-store" }
  );

  if (!res.ok) {
    return null;
  }

  return res.json();
}

interface Props {
  params: { id: string };
}

export default async function MoviePage({ params }: Props) {
  const movie = await getMovie(params.id);

  if (!movie) {
    notFound(); // Redirige a 404 si no encuentra película
  }

  return (
    <main className="max-w-5xl mx-auto px-4 py-8">
      <div className="flex gap-6 mb-8">
        <div className="w-48 aspect-[2/3] bg-neutral-800 rounded-xl overflow-hidden">
          <img
            src={`https://image.tmdb.org/t/p/w500${movie.poster_path}`}
            alt={movie.title}
            className="w-full h-full object-cover"
          />
        </div>

        <div>
          <h1 className="text-3xl font-bold mb-2">{movie.title}</h1>
          <p className="text-neutral-400 mb-4">{movie.release_date}</p>
          <p className="max-w-md text-sm text-neutral-300">{movie.overview}</p>

          <div className="mt-4">
            <h2 className="font-semibold mb-2">Géneros:</h2>
            <ul className="flex gap-2 flex-wrap">
              {movie.genres.map((genre) => (
                <li
                  key={genre.id}
                  className="bg-neutral-800 px-3 py-1 rounded-full text-xs"
                >
                  {genre.name}
                </li>
              ))}
            </ul>
          </div>
        </div>
      </div>
    </main>
  );
}
