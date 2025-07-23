import asyncio
from dotenv import load_dotenv
from livekit.agents import AutoSubscribe, JobContext, WorkerOptions, cli, llm
from livekit.plugins import openai, silero

load_dotenv()

async def entrypoint(ctx: JobContext):
    await ctx.connect(auto_subscribe=AutoSubscribe.AUDIO_ONLY)

    vad = silero.VAD.load()
    stt = openai.STT()
    tts = openai.TTS()
    llm_model = openai.LLM()

    chat_ctx = llm.ChatContext().append(
        role="system",
        text=(
            "You are a home assistant that answers general questions. "
            "Keep responses short and voice-friendly."
        )
    )

    await ctx.say("Hello! How can I help you today?", allow_interruptions=True)

    async for audio in ctx.stream.speech(vad=vad, stt=stt):
        user_input = audio.text
        print(f"User said: {user_input}")

        response = await llm_model.chat(
            chat_ctx,
            user_input,
        )

        await ctx.say(response.text)

if __name__ == "__main__":
    cli.run_app(WorkerOptions(entrypoint_fnc=entrypoint))